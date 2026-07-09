function Card({ title, subtitle, children, className = "" }) {
  return (
    <div className={`rounded-xl border border-slate-200 bg-white shadow-sm ${className}`}>
      {(title || subtitle) && (
        <div className="border-b border-slate-200 px-6 py-4">
          {title && <h2 className="text-lg font-semibold text-slate-800">{title}</h2>}
          {subtitle && <p className="mt-1 text-sm text-slate-500">{subtitle}</p>}
        </div>
      )}

      <div className="p-6">{children}</div>
    </div>
  );
}

export default Card;