import { useEffect, useState } from "react";
import {
    getOverview,
    getCharts,
    runBatch,
} from "../services/api";

import KpiCard from "../components/dashboard/KpiCard";
import DashboardCharts from "../components/dashboard/DashboardCharts";

function Dashboard() {
    const [overview, setOverview] = useState({});
    const [charts, setCharts] = useState({});
    const [loading, setLoading] = useState(true);
    const [running, setRunning] = useState(false);

    useEffect(() => {
        loadDashboard();
    }, []);

    async function loadDashboard() {
        try {
            setLoading(true);

            const overviewRes = await getOverview();
            const chartRes = await getCharts();

            setOverview(overviewRes.data.data);
            setCharts(chartRes.data.data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    }

    async function executeBatch() {
        try {
            setRunning(true);

            await runBatch(1);

            await loadDashboard();
        } catch (err) {
            console.error(err);
        } finally {
            setRunning(false);
        }
    }

    if (loading) {
        return (
            <div className="text-center py-20 text-xl">
                Loading Dashboard...
            </div>
        );
    }

    return (
        <div className="space-y-8">

            <div className="flex justify-between items-center">

                <h1 className="text-3xl font-bold">
                    Enterprise Reconciliation Dashboard
                </h1>

                <button
                    onClick={executeBatch}
                    disabled={running}
                    className="bg-blue-600 text-white px-5 py-2 rounded-lg"
                >
                    {running
                        ? "Running..."
                        : "Run Reconciliation"}
                </button>

            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-5">

                <KpiCard
                    title="Files"
                    value={overview.total_files}
                />

                <KpiCard
                    title="Ready Files"
                    value={overview.ready_files}
                />

                <KpiCard
                    title="Records"
                    value={overview.total_records}
                />

                <KpiCard
                    title="Matched"
                    value={overview.matched_records}
                />

                <KpiCard
                    title="Exceptions"
                    value={overview.exception_records}
                />

                <KpiCard
                    title="Batch Status"
                    value={overview.batch_status}
                />

            </div>

            <DashboardCharts chartData={charts} />

        </div>
    );
}

export default Dashboard;