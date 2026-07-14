function ExceptionFilters({
  search,
  setSearch,
  status,
  setStatus,
  priority,
  setPriority,
  severity,
  setSeverity,
  businessDate,
  setBusinessDate,
  shown,
  total,
}) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-5">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search case, txn, consumer..."
          className="rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"
        />

        <input
          type="date"
          value={businessDate}
          onChange={(e) => setBusinessDate(e.target.value)}
          className="rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"
        />

        <select
          value={status}
          onChange={(e) => setStatus(e.target.value)}
          className="rounded-lg border px-4 py-3"
        >
          <option value="">All Status</option>
          <option value="OPEN">Open</option>
          <option value="ASSIGNED">Assigned</option>
          <option value="RESOLVED">Resolved</option>
          <option value="VERIFIED">Verified</option>
          <option value="CLOSED">Closed</option>
        </select>

        <select
          value={priority}
          onChange={(e) => setPriority(e.target.value)}
          className="rounded-lg border px-4 py-3"
        >
          <option value="">All Priority</option>
          <option value="LOW">Low</option>
          <option value="MEDIUM">Medium</option>
          <option value="HIGH">High</option>
          <option value="CRITICAL">Critical</option>
        </select>

        <select
          value={severity}
          onChange={(e) => setSeverity(e.target.value)}
          className="rounded-lg border px-4 py-3"
        >
          <option value="">All Severity</option>
          <option value="LOW">Low</option>
          <option value="MEDIUM">Medium</option>
          <option value="HIGH">High</option>
        </select>
      </div>

      <p className="mt-4 text-sm text-slate-500">
        Showing <strong>{shown}</strong> of <strong>{total}</strong> exceptions.
      </p>
    </div>
  );
}

export default ExceptionFilters;