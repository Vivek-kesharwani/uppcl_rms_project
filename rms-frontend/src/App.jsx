import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom";

import DashboardLayout from "./components/layout/DashboardLayout";
import ProtectedRoute from "./routes/ProtectedRoute";

import Dashboard from "./pages/Dashboard";
import Exceptions from "./pages/Exceptions";
import Login from "./pages/Login";
import NotFound from "./pages/NotFound";
import ReconciliationWorkbench from "./pages/ReconciliationWorkbench";
import Reports from "./pages/Reports";
import Search from "./pages/Search";
import Upload from "./pages/Upload";
import UploadHistory from "./pages/UploadHistory";

import AgencyDashboard from "./pages/Agency/AgencyDashboard";
import DiscomDashboard from "./pages/Discom/DiscomDashboard";
import HqDashboard from "./pages/hq/HqDashboard";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />

        <Route
          element={
            <ProtectedRoute>
              <DashboardLayout />
            </ProtectedRoute>
          }
        >
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/hq/dashboard" element={<HqDashboard />} />
          <Route path="/discom/dashboard" element={<DiscomDashboard />} />
          <Route path="/agency/dashboard" element={<AgencyDashboard />} />

          <Route path="/upload" element={<Upload />} />
          <Route path="/upload-history" element={<UploadHistory />} />
          <Route path="/reconciliation" element={<ReconciliationWorkbench />} />
          <Route path="/results" element={<div>Result Repository</div>} />
          <Route path="/exceptions" element={<Exceptions />} />
          <Route path="/reports" element={<Reports />} />
          <Route path="/search" element={<Search />} />
          <Route path="/audit-logs" element={<div>Audit Logs</div>} />
        </Route>

        <Route path="/home" element={<Navigate to="/dashboard" replace />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;