function Button({
  children,
  type = "button",
  variant = "primary",
  disabled = false,
  onClick,
  className = "",
}) {
  const variants = {
    primary: "bg-blue-600 text-white hover:bg-blue-700",
    secondary: "bg-white text-slate-700 border border-slate-300 hover:bg-slate-50",
    danger: "bg-red-600 text-white hover:bg-red-700",
    success: "bg-green-600 text-white hover:bg-green-700",
  };

  return (
    <button
      type={type}
      disabled={disabled}
      onClick={onClick}
      className={`inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-60 ${variants[variant]} ${className}`}
    >
      {children}
    </button>
  );
}

export default Button;