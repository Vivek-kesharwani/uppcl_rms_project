function ExceptionToolbar({ onRefresh, onExport }) {
  return (
    <div className="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between">
      <div>
        <h2 className="text-lg font-semibold text-slate-800">
          Exception Queue
        </h2>
        <p className="text-sm text-slate-500">
          Review, assign, resolve and track reconciliation exceptions.
        </p>
      </div>

      <div className="flex gap-3">
        <button
          onClick={onRefresh}
          className="rounded-lg border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
        >
          Refresh
        </button>

        <button
          onClick={onExport}
          className="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700"
        >
          Export CSV
        </button>
      </div>
    </div>
  );
}

export default ExceptionToolbar;