function AuditTable({ logs = [], onView }) {
  return (
    <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
      <table className="min-w-full text-sm">
        <thead className="bg-slate-100">
          <tr>
            <th className="p-4 text-left">Time</th>
            <th className="p-4 text-left">User</th>
            <th className="p-4 text-left">Module</th>
            <th className="p-4 text-left">Action</th>
            <th className="p-4 text-left">Description</th>
            <th className="p-4 text-left">IP</th>
            <th className="p-4 text-left">View</th>
          </tr>
        </thead>

        <tbody>
          {logs.map((log) => (
            <tr
              key={log.id}
              className="border-b hover:bg-slate-50"
            >
              <td className="p-4">
                {new Date(log.created_at).toLocaleString()}
              </td>

              <td className="p-4">
                <div className="font-semibold">
                  {log.user?.name || "System"}
                </div>

                <div className="text-xs text-slate-500">
                  {log.user?.email}
                </div>
              </td>

              <td className="p-4">{log.module}</td>

              <td className="p-4">{log.action}</td>

              <td className="p-4">{log.description}</td>

              <td className="p-4">{log.ip_address}</td>

              <td className="p-4">
                <button
                  onClick={() => onView(log)}
                  className="rounded-lg bg-blue-600 px-3 py-2 text-white"
                >
                  View
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default AuditTable;