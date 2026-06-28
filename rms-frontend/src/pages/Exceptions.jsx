import { useEffect, useState } from "react";
import api from "../services/api";
import PageHeader from "../components/common/PageHeader";
import StatusBadge from "../components/common/StatusBadge";

function Exceptions() {
  const [exceptions, setExceptions] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadExceptions();
  }, []);

  const loadExceptions = async () => {
    try {
      const response = await api.get("/exceptions");
      setExceptions(response.data.data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <p>Loading exceptions...</p>;

  return (
    <div>
      <PageHeader
        title="Exception Management"
        subtitle="Monitor and manage reconciliation exceptions"
      />

      <div className="bg-white rounded-xl shadow border border-slate-200 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-slate-100 text-slate-600">
            <tr>
              <th className="p-4 text-left">Txn ID</th>
              <th className="p-4 text-left">Code</th>
              <th className="p-4 text-left">Severity</th>
              <th className="p-4 text-left">Status</th>
              <th className="p-4 text-left">Assigned Role</th>
              <th className="p-4 text-left">Assigned To</th>
            </tr>
          </thead>

          <tbody>
            {exceptions.map((item) => (
              <tr key={item.id} className="border-b hover:bg-slate-50">
                <td className="p-4 font-semibold">{item.txn_id}</td>
                <td className="p-4">{item.exception_code}</td>
                <td className="p-4">
                  <StatusBadge value={item.severity} />
                </td>
                <td className="p-4">
                  <StatusBadge value={item.status} />
                </td>
                <td className="p-4">{item.assigned_role ?? "-"}</td>
                <td className="p-4">{item.assigned_to ?? "-"}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default Exceptions;