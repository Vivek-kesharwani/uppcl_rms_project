function KpiCard({ title, value, subtitle }) {
  return (
    <div className="bg-white rounded-xl shadow p-5 border border-slate-200">
      <p className="text-sm text-slate-500">{title}</p>
      <h2 className="text-3xl font-bold text-slate-800 mt-2">{value}</h2>
      {subtitle && <p className="text-xs text-slate-400 mt-2">{subtitle}</p>}
    </div>
  );
}

export default KpiCard;