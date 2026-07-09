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

import Card from "../common/Card";

const COLORS = ["#2563eb", "#dc2626", "#16a34a", "#f59e0b", "#7c3aed"];

function DashboardCharts({ chartData }) {
  const resultStatus = chartData?.result_status || [];
  const exceptionTypes = chartData?.exception_types || [];
  const processingStatus = chartData?.processing_status || [];
  const reconciliationStatus = chartData?.reconciliation_status || [];

  return (
    <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
      <Card title="Result Status">
        <ResponsiveContainer width="100%" height={260}>
          <PieChart>
            <Pie
              data={resultStatus}
              dataKey="total"
              nameKey="result_status"
              outerRadius={90}
              label
            >
              {resultStatus.map((_, index) => (
                <Cell key={index} fill={COLORS[index % COLORS.length]} />
              ))}
            </Pie>
            <Tooltip />
            <Legend />
          </PieChart>
        </ResponsiveContainer>
      </Card>

      <Card title="Exception Types">
        <ResponsiveContainer width="100%" height={260}>
          <BarChart data={exceptionTypes}>
            <XAxis dataKey="exception_code" />
            <YAxis />
            <Tooltip />
            <Bar dataKey="total" fill="#dc2626" />
          </BarChart>
        </ResponsiveContainer>
      </Card>

      <Card title="Processing Status">
        <ResponsiveContainer width="100%" height={260}>
          <BarChart data={processingStatus}>
            <XAxis dataKey="processing_status" />
            <YAxis />
            <Tooltip />
            <Bar dataKey="total" fill="#2563eb" />
          </BarChart>
        </ResponsiveContainer>
      </Card>

      <Card title="Reconciliation Status">
        <ResponsiveContainer width="100%" height={260}>
          <BarChart data={reconciliationStatus}>
            <XAxis dataKey="reconciliation_status" />
            <YAxis />
            <Tooltip />
            <Bar dataKey="total" fill="#16a34a" />
          </BarChart>
        </ResponsiveContainer>
      </Card>
    </div>
  );
}

export default DashboardCharts;