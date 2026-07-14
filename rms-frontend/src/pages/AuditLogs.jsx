import { useEffect, useMemo, useState } from "react";

import {
  getAuditLogs,
  getAuditSummary,
  getAuditFilters,
} from "../services/auditService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";

import AuditSummaryCards from "../components/audit/AuditSummaryCards";
import AuditToolbar from "../components/audit/AuditToolbar";
import AuditFilters from "../components/audit/AuditFilters";
import AuditTable from "../components/audit/AuditTable";
import AuditDetailsModal from "../components/audit/AuditDetailsModal";
import AuditPagination from "../components/audit/AuditPagination";

const initialFilters = {
  search: "",
  module: "",
  action: "",
  dateFrom: "",
  dateTo: "",
};

function AuditLogs() {
  const [logs, setLogs] = useState([]);
  const [summary, setSummary] = useState({});
  const [filterOptions, setFilterOptions] = useState({
    modules: [],
    actions: [],
  });

  const [filters, setFilters] = useState(initialFilters);
  const [appliedFilters, setAppliedFilters] = useState(initialFilters);

  const [selectedLog, setSelectedLog] = useState(null);

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState("");

  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalLogs, setTotalLogs] = useState(0);

  useEffect(() => {
    loadInitialData();
  }, []);

  useEffect(() => {
    loadLogs(currentPage, appliedFilters);
  }, [currentPage, appliedFilters]);

  async function loadInitialData() {
    try {
      setLoading(true);
      setError("");

      const [summaryResponse, filtersResponse] = await Promise.all([
        getAuditSummary(),
        getAuditFilters(),
      ]);

      setSummary(summaryResponse.data?.data || {});

      setFilterOptions({
        modules: filtersResponse.data?.data?.modules || [],
        actions: filtersResponse.data?.data?.actions || [],
      });
    } catch (err) {
      console.error("Audit initial-data error:", err);
      setError("Unable to load audit dashboard information.");
    } finally {
      setLoading(false);
    }
  }

  function buildParams(page, activeFilters) {
    const params = {
      page,
      per_page: 20,
    };

    if (activeFilters.search.trim()) {
      params.search = activeFilters.search.trim();
    }

    if (activeFilters.module) {
      params.module = activeFilters.module;
    }

    if (activeFilters.action) {
      params.action = activeFilters.action;
    }

    if (activeFilters.dateFrom) {
      params.date_from = activeFilters.dateFrom;
    }

    if (activeFilters.dateTo) {
      params.date_to = activeFilters.dateTo;
    }

    return params;
  }

  async function loadLogs(page = 1, activeFilters = appliedFilters) {
    try {
      setRefreshing(true);
      setError("");

      const response = await getAuditLogs(
        buildParams(page, activeFilters)
      );

      const payload = response.data?.data;

      if (Array.isArray(payload)) {
        setLogs(payload);
        setTotalLogs(payload.length);
        setTotalPages(1);
        return;
      }

      setLogs(
        Array.isArray(payload?.data)
          ? payload.data
          : []
      );

      setTotalLogs(
        payload?.total ??
        response.data?.count ??
        0
      );

      setTotalPages(
        payload?.last_page ??
        1
      );
    } catch (err) {
      console.error("Audit-log API error:", err);
      setLogs([]);
      setError(
        err.response?.data?.message ||
          "Unable to load audit logs."
      );
    } finally {
      setRefreshing(false);
    }
  }

  function handleApplyFilters() {
    if (
      filters.dateFrom &&
      filters.dateTo &&
      filters.dateFrom > filters.dateTo
    ) {
      setError("Date From cannot be later than Date To.");
      return;
    }

    setError("");
    setCurrentPage(1);
    setAppliedFilters({ ...filters });
  }

  function handleClearFilters() {
    setFilters(initialFilters);
    setAppliedFilters(initialFilters);
    setCurrentPage(1);
    setError("");
  }

  async function handleRefresh() {
    await Promise.all([
      loadLogs(currentPage, appliedFilters),
      reloadSummary(),
    ]);
  }

  async function reloadSummary() {
    try {
      const response = await getAuditSummary();
      setSummary(response.data?.data || {});
    } catch (err) {
      console.error("Audit summary refresh error:", err);
    }
  }

  function escapeCSVValue(value) {
    if (value === null || value === undefined) {
      return '""';
    }

    return `"${String(value).replaceAll('"', '""')}"`;
  }

  function handleExport() {
    if (logs.length === 0) {
      setError("There are no audit records to export.");
      return;
    }

    const headers = [
      "Date and Time",
      "User",
      "Email",
      "Module",
      "Action",
      "Description",
      "IP Address",
    ];

    const rows = logs.map((log) => [
      log.created_at,
      log.user?.name || "System",
      log.user?.email || "",
      log.module,
      log.action,
      log.description,
      log.ip_address,
    ]);

    const csvContent = [
      headers.map(escapeCSVValue).join(","),
      ...rows.map((row) =>
        row.map(escapeCSVValue).join(",")
      ),
    ].join("\n");

    const blob = new Blob([csvContent], {
      type: "text/csv;charset=utf-8;",
    });

    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");

    link.href = url;
    link.download = `audit_logs_${new Date()
      .toISOString()
      .substring(0, 10)}.csv`;

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);
  }

  const shownLogs = useMemo(
    () => logs.length,
    [logs]
  );

  if (loading) {
    return <LoadingSpinner text="Loading Audit Logs Dashboard..." />;
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Audit Logs Dashboard"
        description="Monitor and review important user and system activities across RMS modules."
      />

      {error && (
        <div className="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {error}
        </div>
      )}

      <AuditSummaryCards summary={summary} />

      <AuditToolbar
        totalLogs={totalLogs}
        shownLogs={shownLogs}
        onRefresh={handleRefresh}
        onExport={handleExport}
        onClear={handleClearFilters}
        refreshing={refreshing}
      />

      <AuditFilters
        filters={filters}
        setFilters={setFilters}
        modules={filterOptions.modules}
        actions={filterOptions.actions}
        onApply={handleApplyFilters}
      />

      {refreshing && logs.length === 0 ? (
        <LoadingSpinner text="Loading audit records..." />
      ) : logs.length === 0 ? (
        <EmptyState
          title="No Audit Records Found"
          description="No audit logs match the selected filters."
        />
      ) : (
        <>
          <AuditTable
            logs={logs}
            onView={setSelectedLog}
          />

          <AuditPagination
            currentPage={currentPage}
            totalPages={totalPages}
            onPageChange={setCurrentPage}
          />
        </>
      )}

      <AuditDetailsModal
        log={selectedLog}
        open={Boolean(selectedLog)}
        onClose={() => setSelectedLog(null)}
      />
    </div>
  );
}

export default AuditLogs;