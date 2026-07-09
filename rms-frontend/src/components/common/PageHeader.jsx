function PageHeader({ title, description, action }) {
  return (
    <div className="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
      <div>
        <h1 className="text-3xl font-bold text-slate-800">{title}</h1>
        {description && <p className="mt-1 text-sm text-slate-500">{description}</p>}
      </div>

      {action && <div>{action}</div>}
    </div>
  );
}

export default PageHeader;