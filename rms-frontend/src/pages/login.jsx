import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { login } from "../services/authService";

function Login() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("hqadmin@uppcl.com");
  const [password, setPassword] = useState("password123");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      const response = await login({ email, password });
      const payload = response.data.data ?? response.data;
      const user = payload.user;

      localStorage.setItem("token", payload.token);
      localStorage.setItem("user", JSON.stringify(user));
      localStorage.setItem("role", user.role || "");
      localStorage.setItem("domain", user.domain || "");

      if (user.domain === "HQ") {
        navigate("/hq/dashboard");
      } else if (user.domain === "DISCOM") {
        navigate("/discom/dashboard");
      } else if (user.domain === "AGENCY") {
        navigate("/agency/dashboard");
      } else {
        navigate("/dashboard");
      }
    } catch (error) {
      setError(error.response?.data?.message ?? "Invalid email or password");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-slate-100 px-4">
      <div className="w-full max-w-md rounded-xl bg-white p-8 shadow-lg">
        <h1 className="text-center text-3xl font-bold text-slate-800">
          UPPCL RMS
        </h1>

        <p className="mb-6 mt-2 text-center text-slate-500">
          Reconciliation Management System
        </p>

        {error && (
          <div className="mb-4 rounded-lg bg-red-100 px-4 py-2 text-sm text-red-700">
            {error}
          </div>
        )}

        <form onSubmit={handleLogin} className="space-y-4">
          <input
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="Email address"
          />

          <input
            className="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Password"
          />

          <button
            disabled={loading}
            className="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {loading ? "Signing in..." : "Login"}
          </button>
        </form>
      </div>
    </div>
  );
}

export default Login;