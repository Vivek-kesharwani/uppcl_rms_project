import { useEffect, useState } from "react";
import {
  getDailyReport,
  getExceptionReport,
  getSettlementReport,
} from "../services/reportService";
import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";
import StatusBadge from "../components/common/StatusBadge";

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
      const [dailyRes, exceptionRes, settlementRes] = await Promise.all([
        api.get("/reports/daily-reconciliation"),
        api.get("/reports/exception-summary"),
        api.get("/reports/settlement-summary"),
      ]);

      setDaily(dailyRes.data.data);
      setExceptions(exceptionRes.data.data);
      setSettlements(settlementRes.data.data);
    } catch (error) {
      console.error("Reports error:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <LoadingSpinner text="Loading reports..." />;

  return (
    <div>
      <PageHeader
        title="Reports & Analytics"
        subtitle="Daily reconciliation, exception, and settlement summaries"
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
          badgeColumns={["severity", "status"]}
        />

        <ReportTable
          title="Settlement Summary"
          data={settlements}
          columns={["payment_gateway", "settlement_status", "total_settlements", "total_amount"]}
          badgeColumns={["settlement_status"]}
        />
      </div>
    </div>
  );
}

function ReportTable({ title, data, columns, badgeColumns = [] }) {
  if (!data || data.length === 0) {
    return (
      <EmptyState
        title={`No ${title} data`}
        message="No report records are available yet."
      />
    );
  }

  return (
    <div className="bg-white rounded-xl shadow border border-slate-200 overflow-hidden">
      <h2 className="text-lg font-semibold p-4 bg-slate-100 text-slate-800">
        {title}
      </h2>

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead className="text-slate-600">
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
                    {badgeColumns.includes(col) ? (
                      <StatusBadge value={row[col]} />
                    ) : (
                      row[col] ?? "-"
                    )}
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