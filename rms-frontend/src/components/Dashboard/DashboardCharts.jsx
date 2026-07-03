import {
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer,
  Legend,
} from "recharts";

function DashboardCharts({ chartData }) {
  const resultStatus = chartData?.result_status || [];
  const exceptionTypes = chartData?.exception_types || [];
  const fileStatus = chartData?.file_status || [];

  return (
    <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-8">
      <div className="bg-white rounded-xl shadow p-5">
        <h2 className="font-semibold mb-4">Result Status</h2>
        <ResponsiveContainer width="100%" height={250}>
          <PieChart>
            <Pie data={resultStatus} dataKey="total" nameKey="result_status" outerRadius={90} label />
            <Tooltip />
            <Legend />
          </PieChart>
        </ResponsiveContainer>
      </div>

      <div className="bg-white rounded-xl shadow p-5">
        <h2 className="font-semibold mb-4">Exception Types</h2>
        <ResponsiveContainer width="100%" height={250}>
          <BarChart data={exceptionTypes}>
            <XAxis dataKey="exception_code" />
            <YAxis />
            <Tooltip />
            <Bar dataKey="total" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      <div className="bg-white rounded-xl shadow p-5">
        <h2 className="font-semibold mb-4">File Status</h2>
        <ResponsiveContainer width="100%" height={250}>
          <BarChart data={fileStatus}>
            <XAxis dataKey="status" />
            <YAxis />
            <Tooltip />
            <Bar dataKey="total" />
          </BarChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

export default DashboardCharts;