function AuditFilters({
  filters,
  setFilters,
  modules = [],
  actions = [],
  onApply,
}) {
  function updateFilter(key, value) {
    setFilters((current) => ({
      ...current,
      [key]: value,
    }));
  }

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
        <div className="xl:col-span-2">
          <label className="mb-2 block text-sm font-semibold text-slate-700">
            Search
          </label>

          <input
            type="text"
            value={filters.search}
            onChange={(event) =>
              updateFilter("search", event.target.value)
            }
            placeholder="Search user, action, description or IP..."
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          />
        </div>

        <div>
          <label className="mb-2 block text-sm font-semibold text-slate-700">
            Module
          </label>

          <select
            value={filters.module}
            onChange={(event) =>
              updateFilter("module", event.target.value)
            }
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          >
            <option value="">All Modules</option>

            {modules.map((module) => (
              <option key={module} value={module}>
                {formatLabel(module)}
              </option>
            ))}
          </select>
        </div>

        <div>
          <label className="mb-2 block text-sm font-semibold text-slate-700">
            Action
          </label>

          <select
            value={filters.action}
            onChange={(event) =>
              updateFilter("action", event.target.value)
            }
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          >
            <option value="">All Actions</option>

            {actions.map((action) => (
              <option key={action} value={action}>
                {formatLabel(action)}
              </option>
            ))}
          </select>
        </div>

        <div>
          <label className="mb-2 block text-sm font-semibold text-slate-700">
            Date From
          </label>

          <input
            type="date"
            value={filters.dateFrom}
            onChange={(event) =>
              updateFilter("dateFrom", event.target.value)
            }
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          />
        </div>

        <div>
          <label className="mb-2 block text-sm font-semibold text-slate-700">
            Date To
          </label>

          <input
            type="date"
            value={filters.dateTo}
            onChange={(event) =>
              updateFilter("dateTo", event.target.value)
            }
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          />
        </div>
      </div>

      <div className="mt-5 flex justify-end">
        <button
          type="button"
          onClick={onApply}
          className="rounded-lg bg-blue-600 px-7 py-3 text-sm font-semibold text-white hover:bg-blue-700"
        >
          Apply Filters
        </button>
      </div>
    </div>
  );
}

function formatLabel(value) {
  return String(value || "")
    .replaceAll("_", " ")
    .toLowerCase()
    .replace(/\b\w/g, (character) => character.toUpperCase());
}

export default AuditFilters;