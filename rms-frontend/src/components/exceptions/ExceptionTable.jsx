import StatusBadge from "../common/StatusBadge";
import ExceptionStatusActions from "./ExceptionStatusActions";

function ExceptionTable({
  exceptions = [],
  onView,
  onEdit,
  onAssign,
  onResolve,
  onVerify,
  onClose,
  onReopen,
}) {
  return (
    <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
      <table className="min-w-full text-sm">
        <thead className="bg-slate-100 text-slate-700">
          <tr>
            <th className="p-4 text-left">Case</th>
            <th className="p-4 text-left">Txn ID</th>
            <th className="p-4 text-left">Consumer</th>
            <th className="p-4 text-left">Matching Set</th>
            <th className="p-4 text-left">Exception</th>
            <th className="p-4 text-left">Priority</th>
            <th className="p-4 text-left">Status</th>
            <th className="p-4 text-left">SLA Due</th>
            <th className="p-4 text-left">Actions</th>
          </tr>
        </thead>

        <tbody>
          {exceptions.map((item) => {
            const result = item.reconciliation_result || {};

            return (
              <tr key={item.id} className="border-b hover:bg-slate-50">
                <td className="p-4 font-semibold">{item.case_number}</td>
                <td className="p-4 font-semibold">
                  {item.txn_id || result.transaction_id || "-"}
                </td>
                <td className="p-4">{result.consumer_number || "-"}</td>
                <td className="p-4">{result.matching_set?.set_name || "-"}</td>
                <td className="p-4">
                  <span className="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                    {item.exception_code}
                  </span>
                </td>
                <td className="p-4">
                  <StatusBadge status={item.priority} />
                </td>
                <td className="p-4">
                  <StatusBadge status={item.status} />
                </td>
                <td className="p-4">
                  {item.sla_due_at?.substring(0, 10) || "-"}
                </td>

                <td className="p-4">
                  <div className="flex flex-col gap-2">
                    <div className="flex gap-2">
                      <button onClick={() => onView(item)} className="action-btn">
                        View
                      </button>
                      <button onClick={() => onEdit(item)} className="action-btn">
                        Edit
                      </button>
                    </div>

                    <ExceptionStatusActions
                      item={item}
                      onAssign={onAssign}
                      onResolve={onResolve}
                      onVerify={onVerify}
                      onClose={onClose}
                      onReopen={onReopen}
                    />
                  </div>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}

export default ExceptionTable;