function AuditToolbar({
  totalLogs = 0,
  shownLogs = 0,
  onRefresh,
  onExport,
  onClear,
  refreshing = false,
}) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 className="text-lg font-semibold text-slate-800">
            System Activity Log
          </h2>

          <p className="mt-1 text-sm text-slate-500">
            Review user actions performed across RMS modules.
          </p>

          <p className="mt-2 text-sm text-blue-600">
            Showing <strong>{shownLogs}</strong> of{" "}
            <strong>{totalLogs}</strong> audit records
          </p>
        </div>

        <div className="flex flex-wrap gap-3">
          <button
            type="button"
            onClick={onRefresh}
            disabled={refreshing}
            className="rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {refreshing ? "Refreshing..." : "Refresh"}
          </button>

          <button
            type="button"
            onClick={onExport}
            className="rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700"
          >
            Export CSV
          </button>

          <button
            type="button"
            onClick={onClear}
            className="rounded-lg bg-slate-700 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800"
          >
            Clear Filters
          </button>
        </div>
      </div>
    </div>
  );
}

export default AuditToolbar;