import {
    PieChart,
    Pie,
    Cell,
    Tooltip,
    Legend,
    ResponsiveContainer,
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    LineChart,
    Line,
} from "recharts";

const COLORS = [
    "#2563eb",
    "#dc2626",
    "#16a34a",
    "#ca8a04",
    "#9333ea",
];

function ReconciliationCharts({
    charts = {},
    daily = [],
    settlements = [],
}) {

    const resultStatus =
        charts.result_status || [];

    const exceptionTypes =
        charts.exception_types || [];

    return (
        <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">    
            {/* Result Distribution */}
            <ChartCard title="Result Distribution">
                <ResponsiveContainer
                    width="100%"
                    height={300}
                >
                    <PieChart>
                        <Pie
                            data={resultStatus}
                            dataKey="total"
                            nameKey="result_status"
                            outerRadius={110}
                            label
                        >
                            {
                                resultStatus.map((_, index) => (
                                    <Cell
                                        key={index}
                                        fill={
                                            COLORS[
                                            index %
                                            COLORS.length
                                            ]
                                        }
                                    />
                                ))
                            }
                        </Pie>
                        <Tooltip />
                        <Legend />
                    </PieChart>
                </ResponsiveContainer>
            </ChartCard>

            {/* Exception Types */}

            <ChartCard
                title="Exception Breakdown"
            >
                <ResponsiveContainer
                    width="100%"
                    height={300}
                >
                    <BarChart
                        data={exceptionTypes}
                    >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis
                            dataKey="exception_code"
                        />
                        <YAxis />
                        <Tooltip />
                        <Bar
                            dataKey="total"
                            fill="#2563eb"
                        />
                    </BarChart>
                </ResponsiveContainer>
            </ChartCard>

            {/* Historical Trend */}

            <ChartCard
                title="Historical Reconciliation Trend"
            >
                <ResponsiveContainer
                    width="100%"
                    height={320}
                >
                    <LineChart
                        data={daily}
                    >
                        <CartesianGrid strokeDasharray="3 3"/>
                        <XAxis
                            dataKey="business_date"
                        />
                        <YAxis />
                        <Tooltip />
                        <Legend />
                        <Line
                            dataKey="matched"
                            stroke="#16a34a"
                            strokeWidth={3}
                        />
                        <Line
                            dataKey="exceptions"
                            stroke="#dc2626"
                            strokeWidth={3}
                        />
                    </LineChart>
                </ResponsiveContainer>
            </ChartCard>

            {/* Settlement */}

            <ChartCard
                title="Settlement Variance"
            >
                <ResponsiveContainer
                    width="100%"
                    height={320}
                >
                    <BarChart
                        data={settlements}
                    >
                        <CartesianGrid strokeDasharray="3 3"/>
                        <XAxis
                            dataKey="exception_code"
                        />
                        <YAxis />
                        <Tooltip />
                        <Bar
                            dataKey="total_variance"
                            fill="#dc2626"
                        />
                    </BarChart>
                </ResponsiveContainer>
            </ChartCard>
        </div>
    );
}

function ChartCard({
    title,
    children,
}) {

    return (
        <div className="bg-white rounded-xl shadow border border-slate-200 p-5">
            <h2 className="text-lg font-semibold mb-5">
                {title}
            </h2>
            {children}
        </div>
    );
}

export default ReconciliationCharts;