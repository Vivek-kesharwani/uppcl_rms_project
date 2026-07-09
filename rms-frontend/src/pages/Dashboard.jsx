import { useEffect, useState } from "react";
import {
    getOverview,
    getCharts,
} from "../services/dashboardService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import KpiCard from "../components/dashboard/KpiCard";
import DashboardCharts from "../components/dashboard/DashboardCharts";
import LatestBatchCard from "../components/dashboard/LatestBatchCard";

function Dashboard({ role }) {
    const [overview, setOverview] = useState(null);
    const [charts, setCharts] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadDashboard();
    }, []);

    async function loadDashboard() {
        try {
            setLoading(true);

            const overviewRes = await getOverview();
            const chartsRes = await getCharts();

            setOverview(overviewRes.data.data);
            setCharts(chartsRes.data.data);

        } catch (error) {
            console.error("Dashboard load failed:", error);
        } finally {
            setLoading(false);
        }
    }

    if (loading) {
        return <LoadingSpinner text="Loading dashboard..." />;
    }

    return (
        <div className="space-y-6">
            <PageHeader
                title="Enterprise Reconciliation Dashboard"
                description={`Logged in as: ${role}`}
            />

            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <KpiCard title="Total Files" value={overview?.total_files} />
                <KpiCard title="Available Files" value={overview?.available_files} />
                <KpiCard title="Reconciled Files" value={overview?.reconciled_files} />
                <KpiCard title="Total Batches" value={overview?.total_batches} />
                <KpiCard title="Total Records" value={overview?.total_records} />
                <KpiCard title="Matched Records" value={overview?.matched_records} />
                <KpiCard title="Exception Records" value={overview?.exception_records} />
                <KpiCard title="Batch Status" value={overview?.batch_status || "-"} />
            </div>

            <LatestBatchCard batch={overview?.batch} />

            <DashboardCharts chartData={charts} />

        </div>
    );
}

export default Dashboard;