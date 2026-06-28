import { useEffect, useState } from "react";
import api from "../services/api";
import KpiCard from "../components/dashboard/KpiCard";
import DashboardCharts from "../components/dashboard/DashboardCharts";
import RecentUploads from "../components/dashboard/RecentUploads";
import RecentExceptions from "../components/dashboard/RecentExceptions";

function Dashboard() {
  const [overview, setOverview] = useState(null);
  const [loading, setLoading] = useState(true);
  const [charts, setCharts] = useState(null);
  const [recentUploads, setRecentUploads] = useState([]);
  const [recentExceptions, setRecentExceptions] = useState([]);

  useEffect(() => {
    fetchOverview();
  }, []);

  const fetchOverview = async () => {
    try {
        const response = await api.get("/dashboard/overview");
        setOverview(response.data.data);
      
        const chartResponse = await api.get("/dashboard/charts");
        setCharts(chartResponse.data.data);
      
        const uploadsResponse = await api.get("/dashboard/recent-uploads");
        setRecentUploads(uploadsResponse.data.data);

        const exceptionsResponse = await api.get("/dashboard/recent-exceptions");
        setRecentExceptions(exceptionsResponse.data.data);
    } catch (error) {
      console.error("Dashboard API error:", error);
    } finally {
      setLoading(false);
    } 
  };

  if (loading) {
    return <p className="text-slate-600">Loading dashboard...</p>;
  }

  return (
    <div>
      <h1 className="text-2xl font-bold text-slate-800 mb-6">Dashboard</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5">
        <KpiCard title="Total Transactions" value={overview?.total_transactions ?? 0} />
        <KpiCard title="Matched" value={overview?.matched ?? 0} />
        <KpiCard title="Exceptions" value={overview?.exceptions ?? 0} />
        <KpiCard title="Amount Mismatch" value={overview?.amount_mismatch ?? 0} />
        <KpiCard title="Missing Settlement" value={overview?.missing_settlement ?? 0} />
      </div>
      <DashboardCharts chartData={charts} />
      <div className="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-8">
        <RecentUploads uploads={recentUploads} />
        <RecentExceptions exceptions={recentExceptions} />
      </div>
    </div>
  );
}

export default Dashboard;