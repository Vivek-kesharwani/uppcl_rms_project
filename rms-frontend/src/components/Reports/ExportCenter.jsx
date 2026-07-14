function ExportCenter({ onExportDaily, onExportException, onExportSettlement }) {
  const exports = [
    {
      title: "Daily Reconciliation Report",
      description: "Export date-wise total, matched and exception counts.",
      action: onExportDaily,
    },
    {
      title: "Exception Summary Report",
      description: "Export exception breakdown by code, priority and status.",
      action: onExportException,
    },
    {
      title: "Settlement / Variance Report",
      description: "Export exception variance and settlement summary.",
      action: onExportSettlement,
    },
  ];

  return (
    <div className="rounded-xl border border-slate-200 bg-white shadow-sm">
      <div className="border-b px-6 py-5">
        <h2 className="text-lg font-semibold text-slate-800">Export Center</h2>
        <p className="mt-1 text-sm text-slate-500">
          Download operational report data as CSV.
        </p>
      </div>

      <div className="grid grid-cols-1 gap-5 p-6 md:grid-cols-3">
        {exports.map((item) => (
          <div
            key={item.title}
            className="rounded-xl border border-slate-200 p-5"
          >
            <h3 className="font-semibold text-slate-800">{item.title}</h3>
            <p className="mt-2 text-sm text-slate-500">{item.description}</p>

            <button
              onClick={item.action}
              className="mt-5 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            >
              Export CSV
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

export default ExportCenter;