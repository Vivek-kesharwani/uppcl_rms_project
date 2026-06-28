import { useEffect, useState } from "react";
import api from "../services/api";
import PageHeader from "../components/common/PageHeader";

function Reports() {
  const [daily, setDaily] = useState([]);
  const [exceptions, setExceptions] = useState([]);
  const [settlements, setSettlements] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadReports();
  }, []);

  const loadReports = async () => {
    try {
      const dailyResponse = await api.get("/reports/daily-reconciliation");
      const exceptionResponse = await api.get("/reports/exception-summary");
      const settlementResponse = await api.get("/reports/settlement-summary");

      setDaily(dailyResponse.data.data);
      setExceptions(exceptionResponse.data.data);
      setSettlements(settlementResponse.data.data);
    } catch (error) {
      console.error("Reports error:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <p>Loading reports...</p>;
  }

  return (
    <div>
      <PageHeader
        title="Reports & Analytics"
        subtitle="Daily reconciliation, settlement and exception reports"
      />

      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <ReportTable
          title="Daily Reconciliation"
          data={daily}
          columns={["date", "total_transactions", "matched", "exceptions"]}
        />

        <ReportTable
          title="Exception Summary"
          data={exceptions}
          columns={["exception_code", "severity", "status", "total", "total_variance"]}
        />

        <ReportTable
          title="Settlement Summary"
          data={settlements}
          columns={["payment_gateway", "settlement_status", "total_settlements", "total_amount"]}
        />
      </div>
    </div>
  );
}

function ReportTable({ title, data, columns }) {
  return (
    <div className="bg-white rounded-xl shadow overflow-hidden">
      <h2 className="text-lg font-semibold p-4 bg-slate-100">{title}</h2>

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr>
              {columns.map((col) => (
                <th key={col} className="p-3 text-left border-b">
                  {col.replaceAll("_", " ").toUpperCase()}
                </th>
              ))}
            </tr>
          </thead>

          <tbody>
            {data.map((row, index) => (
              <tr key={index} className="border-b hover:bg-slate-50">
                {columns.map((col) => (
                  <td key={col} className="p-3">
                    {row[col] ?? "-"}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default Reports;