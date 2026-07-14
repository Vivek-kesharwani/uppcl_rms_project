import { useEffect, useMemo, useState } from "react";

import {
  getDailyReport,
  getExceptionReport,
  getSettlementReport,
} from "../services/reportService";

import {
  getCharts,
  getBatches,
} from "../services/dashboardService";

import {
  getMatchingSets,
} from "../services/reconciliationService";

import {
  getResultFiles,
  downloadResultFile,
} from "../services/resultService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";

import ReportSearchPanel from "../components/reports/ReportSearchPanel";
import LatestReconCard from "../components/reports/LatestReconCard";
import ReportKPICards from "../components/reports/ReportKPICards";
import ReconciliationCharts from "../components/reports/ReconciliationCharts";
import HistoricalBatchTable from "../components/reports/HistoricalBatchTable";
import ResultRepositoryTable from "../components/reports/ResultRepositoryTable";
import ExportCenter from "../components/reports/ExportCenter";

function Reports() {
  const [daily, setDaily] = useState([]);
  const [exceptions, setExceptions] = useState([]);
  const [settlements, setSettlements] = useState([]);
  const [charts, setCharts] = useState({});
  const [batches, setBatches] = useState([]);
  const [matchingSets, setMatchingSets] = useState([]);
  const [resultFiles, setResultFiles] = useState([]);

  const [selectedBatch, setSelectedBatch] = useState(null);
  const [activeResultId, setActiveResultId] = useState("");

  const [search, setSearch] = useState("");
  const [matchingSetId, setMatchingSetId] = useState("");
  const [businessDate, setBusinessDate] = useState("");
  const [period, setPeriod] = useState("");

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    loadReports();
  }, []);

  async function loadReports() {
    try {
      setLoading(true);
      setError("");

      const [
        dailyRes,
        exceptionRes,
        settlementRes,
        chartRes,
        batchRes,
        matchingSetRes,
        resultFileRes,
      ] = await Promise.all([
        getDailyReport(),
        getExceptionReport(),
        getSettlementReport(),
        getCharts(),
        getBatches(),
        getMatchingSets(),
        getResultFiles(),
      ]);

      const dailyData = normaliseArray(dailyRes.data?.data);
      const exceptionData = normaliseArray(exceptionRes.data?.data);
      const settlementData = normaliseArray(settlementRes.data?.data);
      const chartData = chartRes.data?.data || {};
      const batchData = normaliseArray(batchRes.data?.data);
      const matchingSetData = normaliseArray(matchingSetRes.data?.data);
      const resultFileData = normaliseArray(resultFileRes.data?.data);

      setDaily(dailyData);
      setExceptions(exceptionData);
      setSettlements(settlementData);
      setCharts(chartData);
      setBatches(batchData);
      setMatchingSets(matchingSetData);
      setResultFiles(resultFileData);

      const latestBatch = batchData[0] || null;
      setSelectedBatch(latestBatch);

      const latestResult =
        resultFileData.find(
          (file) => String(file.batch_id) === String(latestBatch?.id)
        ) || resultFileData[0];

      setActiveResultId(latestResult ? String(latestResult.id) : "");
    } catch (requestError) {
      console.error("Reports dashboard error:", requestError);

      setError(
        requestError.response?.data?.message ||
          "Failed to load the reports dashboard."
      );
    } finally {
      setLoading(false);
    }
  }

  const filteredBatches = useMemo(() => {
    const query = search.trim().toLowerCase();

    return batches.filter((batch) => {
      const matchingSetName = getBatchMatchingSetName(batch);

      const searchableText = [
        batch.batch_code,
        batch.status,
        batch.batch_type,
        batch.business_date,
        matchingSetName,
      ]
        .filter(Boolean)
        .join(" ")
        .toLowerCase();

      const matchesSearch =
        !query || searchableText.includes(query);

      const matchesDate =
        !businessDate ||
        batch.business_date?.substring(0, 10) === businessDate;

      const matchesPeriod =
        !period || batch.batch_type === period;

      const matchesMatchingSet =
        !matchingSetId ||
        batchHasMatchingSet(batch, matchingSetId);

      return (
        matchesSearch &&
        matchesDate &&
        matchesPeriod &&
        matchesMatchingSet
      );
    });
  }, [
    batches,
    search,
    businessDate,
    period,
    matchingSetId,
  ]);

  const filteredResultFiles = useMemo(() => {
    const query = search.trim().toLowerCase();

    return resultFiles.filter((item) => {
      const setName =
        item.matching_set?.set_name ||
        item.matchingSet?.set_name ||
        "";

      const searchableText = [
        item.file_name,
        item.batch?.batch_code,
        setName,
        item.business_date,
        item.business_month,
        item.status,
      ]
        .filter(Boolean)
        .join(" ")
        .toLowerCase();

      const matchesSearch =
        !query || searchableText.includes(query);

      const matchesDate =
        !businessDate ||
        item.business_date?.substring(0, 10) === businessDate;

      const matchesSet =
        !matchingSetId ||
        String(item.matching_set_id) === String(matchingSetId);

      const batchType = item.batch?.batch_type || "";

      const matchesPeriod =
        !period ||
        batchType === period ||
        item.file_name
          ?.toUpperCase()
          .includes(period);

      return (
        matchesSearch &&
        matchesDate &&
        matchesSet &&
        matchesPeriod
      );
    });
  }, [
    resultFiles,
    search,
    businessDate,
    matchingSetId,
    period,
  ]);

  const selectedResultFile =
    filteredResultFiles.find(
      (file) => String(file.id) === String(activeResultId)
    ) ||
    filteredResultFiles.find(
      (file) =>
        String(file.batch_id) === String(selectedBatch?.id)
    ) ||
    filteredResultFiles[0] ||
    null;

  function handleSearch() {
    const selectedFile =
      filteredResultFiles.find(
        (file) => String(file.id) === String(activeResultId)
      ) ||
      filteredResultFiles[0];

    if (!selectedFile) {
      setError("No matching result file was found.");
      return;
    }

    setError("");
    setActiveResultId(String(selectedFile.id));

    const relatedBatch = batches.find(
      (batch) =>
        String(batch.id) === String(selectedFile.batch_id)
    );

    if (relatedBatch) {
      setSelectedBatch(relatedBatch);
    }
  }

  function handleResultSelection(resultId) {
    setActiveResultId(resultId);

    const selectedFile = resultFiles.find(
      (file) => String(file.id) === String(resultId)
    );

    if (!selectedFile) {
      return;
    }

    const relatedBatch = batches.find(
      (batch) =>
        String(batch.id) === String(selectedFile.batch_id)
    );

    if (relatedBatch) {
      setSelectedBatch(relatedBatch);
    }
  }

  function handleViewBatch(batch) {
    setSelectedBatch(batch);
    setError("");

    const relatedFile = resultFiles.find(
      (item) =>
        String(item.batch_id) === String(batch.id)
    );

    setActiveResultId(
      relatedFile ? String(relatedFile.id) : ""
    );

    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  }

  function resetFilters() {
    setSearch("");
    setMatchingSetId("");
    setBusinessDate("");
    setPeriod("");
    setError("");

    const latestBatch = batches[0] || null;
    setSelectedBatch(latestBatch);

    const relatedFile =
      resultFiles.find(
        (item) =>
          String(item.batch_id) === String(latestBatch?.id)
      ) || resultFiles[0];

    setActiveResultId(
      relatedFile ? String(relatedFile.id) : ""
    );
  }

  function exportCSV(fileName, rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
      setError("There is no report data available to export.");
      return;
    }

    setError("");

    const headers = Object.keys(rows[0]);

    const csvRows = rows.map((row) =>
      headers
        .map((header) => {
          const value = row[header] ?? "";
          const escaped = String(value).replaceAll('"', '""');

          return `"${escaped}"`;
        })
        .join(",")
    );

    const csv = [
      headers.join(","),
      ...csvRows,
    ].join("\n");

    const blob = new Blob([csv], {
      type: "text/csv;charset=utf-8;",
    });

    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");

    link.href = url;
    link.download = fileName;

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);
  }

  async function handleDownloadResult(item) {
    try {
      setError("");

      const response = await downloadResultFile(item.id);

      const blob = new Blob([response.data], {
        type:
          response.headers?.["content-type"] ||
          "text/csv",
      });

      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");

      link.href = url;
      link.download =
        item.file_name || "reconciliation-result.csv";

      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      URL.revokeObjectURL(url);
    } catch (downloadError) {
      console.error("Result download failed:", downloadError);

      setError(
        downloadError.response?.data?.message ||
          "Result file download failed."
      );
    }
  }

  if (loading) {
    return (
      <LoadingSpinner text="Loading Reports Dashboard..." />
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Reports Dashboard"
        description="MIS analytics, historical reconciliation batches and generated result files."
      />

      {error && (
        <div className="rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
          {error}
        </div>
      )}

      <ReportSearchPanel
        search={search}
        setSearch={setSearch}
        matchingSetId={matchingSetId}
        setMatchingSetId={setMatchingSetId}
        businessDate={businessDate}
        setBusinessDate={setBusinessDate}
        period={period}
        setPeriod={setPeriod}
        matchingSets={matchingSets}
        relatedResults={filteredResultFiles}
        selectedResultId={activeResultId}
        setSelectedResultId={handleResultSelection}
        onSearch={handleSearch}
        onReset={resetFilters}
      />

      <LatestReconCard
        batch={selectedBatch}
        resultFile={selectedResultFile}
      />

      <ReportKPICards batch={selectedBatch} />

      <ReconciliationCharts
        charts={charts}
        daily={daily}
        settlements={settlements}
      />

      <HistoricalBatchTable
        batches={filteredBatches}
        onView={handleViewBatch}
      />

      <ResultRepositoryTable
        resultFiles={filteredResultFiles}
        onDownload={handleDownloadResult}
      />

      <ExportCenter
        onExportDaily={() =>
          exportCSV(
            "daily-reconciliation-report.csv",
            daily
          )
        }
        onExportException={() =>
          exportCSV(
            "exception-summary-report.csv",
            exceptions
          )
        }
        onExportSettlement={() =>
          exportCSV(
            "settlement-summary-report.csv",
            settlements
          )
        }
      />
    </div>
  );
}

function normaliseArray(payload) {
  if (Array.isArray(payload)) {
    return payload;
  }

  if (Array.isArray(payload?.data)) {
    return payload.data;
  }

  return [];
}

function batchHasMatchingSet(batch, matchingSetId) {
  const batchFiles = Array.isArray(batch.batch_files)
    ? batch.batch_files
    : Array.isArray(batch.batchFiles)
      ? batch.batchFiles
      : [];

  if (String(batch.matching_set_id) === String(matchingSetId)) {
    return true;
  }

  return batchFiles.some(
    (file) =>
      String(file.matching_set_id) ===
      String(matchingSetId)
  );
}

function getBatchMatchingSetName(batch) {
  return (
    batch.matching_set?.set_name ||
    batch.matchingSet?.set_name ||
    ""
  );
}

export default Reports;