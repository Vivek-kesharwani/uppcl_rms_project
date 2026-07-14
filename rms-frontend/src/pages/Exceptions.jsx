import { useEffect, useMemo, useState } from "react";
import {
  getExceptions,
  updateException,
  assignException,
  resolveException,
  verifyException,
  closeException,
  reopenException,
} from "../services/exceptionService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";

import ExceptionSummaryCards from "../components/exceptions/ExceptionSummaryCards";
import ExceptionToolbar from "../components/exceptions/ExceptionToolbar";
import ExceptionFilters from "../components/exceptions/ExceptionFilters";
import ExceptionTable from "../components/exceptions/ExceptionTable";
import ExceptionDetailsModal from "../components/exceptions/ExceptionDetailsModal";
import ExceptionEditModal from "../components/exceptions/ExceptionEditModal";
import ExceptionAssignModal from "../components/exceptions/ExceptionAssignModal";
import ExceptionResolveModal from "../components/exceptions/ExceptionResolveModal";

function Exceptions() {
  const [exceptions, setExceptions] = useState([]);
  const [loading, setLoading] = useState(true);

  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("");
  const [priority, setPriority] = useState("");
  const [severity, setSeverity] = useState("");
  const [businessDate, setBusinessDate] = useState("");

  const [viewItem, setViewItem] = useState(null);
  const [editItem, setEditItem] = useState(null);
  const [assignItem, setAssignItem] = useState(null);
  const [resolveItem, setResolveItem] = useState(null);

  useEffect(() => {
    loadExceptions();
  }, []);

  async function loadExceptions() {
    try {
      setLoading(true);
      const response = await getExceptions();
      const payload = response.data?.data;

      const list = Array.isArray(payload)
        ? payload
        : Array.isArray(payload?.data)
        ? payload.data
        : [];

      setExceptions(list);
    } catch (error) {
      console.error(error);
      setExceptions([]);
    } finally {
      setLoading(false);
    }
  }

  const filtered = useMemo(() => {
    return exceptions.filter((item) => {
      const result = item.reconciliation_result || {};

      const text = `
        ${item.case_number || ""}
        ${item.txn_id || ""}
        ${result.consumer_number || ""}
        ${item.exception_code || ""}
        ${result.batch?.batch_code || ""}
        ${result.matching_set?.set_name || ""}
      `.toLowerCase();

      const itemDate =
        result.business_date?.substring(0, 10) ||
        item.opened_at?.substring(0, 10);

      return (
        text.includes(search.toLowerCase()) &&
        (!status || item.status === status) &&
        (!priority || item.priority === priority) &&
        (!severity || item.severity === severity) &&
        (!businessDate || itemDate === businessDate)
      );
    });
  }, [exceptions, search, status, priority, severity, businessDate]);

  async function handleEdit(id, payload) {
    await updateException(id, payload);
    setEditItem(null);
    loadExceptions();
  }

  async function handleAssign(id, payload) {
    await assignException(id, payload);
    setAssignItem(null);
    loadExceptions();
  }

  async function handleResolve(id, payload) {
    await resolveException(id, payload);
    setResolveItem(null);
    loadExceptions();
  }

  async function handleVerify(item) {
    await verifyException(item.id);
    loadExceptions();
  }

  async function handleClose(item) {
    await closeException(item.id);
    loadExceptions();
  }

  async function handleReopen(item) {
    await reopenException(item.id);
    loadExceptions();
  }

  function exportCSV() {
    const rows = filtered.map((e) => ({
      case_number: e.case_number,
      txn_id: e.txn_id,
      exception_code: e.exception_code,
      priority: e.priority,
      status: e.status,
      variance_amount: e.variance_amount,
    }));

    const csv =
      "Case,Txn,Exception,Priority,Status,Variance\n" +
      rows
        .map((r) =>
          [
            r.case_number,
            r.txn_id,
            r.exception_code,
            r.priority,
            r.status,
            r.variance_amount,
          ].join(",")
        )
        .join("\n");

    const blob = new Blob([csv], { type: "text/csv" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "exceptions.csv";
    a.click();

    URL.revokeObjectURL(url);
  }

  if (loading) {
    return <LoadingSpinner text="Loading Exception Management..." />;
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Exception Management Center"
        description="View, assign, update, resolve and track reconciliation exceptions."
      />

      <ExceptionSummaryCards exceptions={exceptions} />

      <ExceptionToolbar onRefresh={loadExceptions} onExport={exportCSV} />

      <ExceptionFilters
        search={search}
        setSearch={setSearch}
        status={status}
        setStatus={setStatus}
        priority={priority}
        setPriority={setPriority}
        severity={severity}
        setSeverity={setSeverity}
        businessDate={businessDate}
        setBusinessDate={setBusinessDate}
        shown={filtered.length}
        total={exceptions.length}
      />

      {filtered.length === 0 ? (
        <EmptyState
          title="No Exceptions Found"
          description="No exception records match the selected filters."
        />
      ) : (
        <ExceptionTable
          exceptions={filtered}
          onView={setViewItem}
          onEdit={setEditItem}
          onAssign={setAssignItem}
          onResolve={setResolveItem}
          onVerify={handleVerify}
          onClose={handleClose}
          onReopen={handleReopen}
        />
      )}

      <ExceptionDetailsModal item={viewItem} onClose={() => setViewItem(null)} />

      <ExceptionEditModal
        item={editItem}
        onClose={() => setEditItem(null)}
        onSave={handleEdit}
      />

      <ExceptionAssignModal
        item={assignItem}
        onClose={() => setAssignItem(null)}
        onAssign={handleAssign}
      />

      <ExceptionResolveModal
        item={resolveItem}
        onClose={() => setResolveItem(null)}
        onResolve={handleResolve}
      />
    </div>
  );
}

export default Exceptions;