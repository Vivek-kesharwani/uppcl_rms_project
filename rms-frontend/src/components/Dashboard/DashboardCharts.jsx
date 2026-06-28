import {
  BarChart,
  Bar,
  PieChart,
  Pie,
  Tooltip,
  ResponsiveContainer,
  XAxis,
  YAxis,
  Legend,
} from "recharts";

function DashboardCharts({ chartData }) {
  const exceptionStatus = chartData?.exception_status || [];
  const reconciliationStatus = chartData?.reconciliation_status || [];

  return (
    <div className="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-8">
      <div className="bg-white rounded-xl shadow p-6 border border-slate-200 min-h-[360px]">
        <h2 className="text-lg font-semibold text-slate-800 mb-4">
          Reconciliation Status
        </h2>

        <div className="w-full h-[280px]">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={reconciliationStatus}>
              <XAxis dataKey="recon_status" />
              <YAxis />
              <Tooltip />
              <Legend />
              <Bar dataKey="total" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow p-6 border border-slate-200 min-h-[360px]">
        <h2 className="text-lg font-semibold text-slate-800 mb-4">
          Exception Status
        </h2>

        <div className="w-full h-[280px]">
          <ResponsiveContainer width="100%" height="100%">
            <PieChart>
              <Pie
                data={exceptionStatus}
                dataKey="total"
                nameKey="status"
                cx="50%"
                cy="50%"
                outerRadius={90}
                label
              />
              <Tooltip />
              <Legend />
            </PieChart>
          </ResponsiveContainer>
        </div>
      </div>
    </div>
  );
}

export default DashboardCharts;