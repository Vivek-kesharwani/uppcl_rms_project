import { useEffect, useMemo, useState } from "react";
import { getResultFiles } from "../services/resultService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";
import ResultFilters from "../components/results/ResultFilters";
import ResultTable from "../components/results/ResultTable";

function ResultRepository() {
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(true);

  const [search, setSearch] = useState("");
  const [businessDate, setBusinessDate] = useState("");
  const [fileType, setFileType] = useState("");

  useEffect(() => {
    loadResults();
  }, []);

  async function loadResults() {
    try {
      setLoading(true);

      const response = await getResultFiles();

      const payload = response.data.data;

      if (Array.isArray(payload)) {
        setResults(payload);
      } else {
        setResults(payload?.data || []);
      }
    } catch (error) {
      console.error("Result files error:", error);
    } finally {
      setLoading(false);
    }
  }

  const filteredResults = useMemo(() => {
    return results.filter((item) => {
      const searchText = `${item.file_name || ""} ${item.batch?.batch_code || ""} ${
        item.matching_set?.set_name || ""
      }`.toLowerCase();

      const matchesSearch = searchText.includes(search.toLowerCase());

      const matchesDate =
        !businessDate ||
        item.business_date?.substring(0, 10) === businessDate;

      const matchesType =
        !fileType ||
        item.batch?.batch_type === fileType ||
        item.file_name?.toLowerCase().includes(fileType.toLowerCase());

      return matchesSearch && matchesDate && matchesType;
    });
  }, [results, search, businessDate, fileType]);

  if (loading) {
    return <LoadingSpinner text="Loading result repository..." />;
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Result Repository"
        description="View generated reconciliation result files and batch output summaries."
      />

      <ResultFilters
        search={search}
        setSearch={setSearch}
        businessDate={businessDate}
        setBusinessDate={setBusinessDate}
        fileType={fileType}
        setFileType={setFileType}
        onRefresh={loadResults}
        total={results.length}
        shown={filteredResults.length}
      />

      {filteredResults.length === 0 ? (
        <EmptyState
          title="No Result Files Found"
          message="No reconciliation result files match the selected filters."
        />
      ) : (
        <ResultTable results={filteredResults} />
      )}
    </div>
  );
}

export default ResultRepository;