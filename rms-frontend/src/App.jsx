import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";

import DashboardLayout from "./components/layout/DashboardLayout";
import ProtectedRoute from "./routes/ProtectedRoute";

import Login from "./pages/Login";
import Dashboard from "./pages/Dashboard";
import Upload from "./pages/Upload";
import UploadHistory from "./pages/UploadHistory";
import Exceptions from "./pages/Exceptions";
import Search from "./pages/Search";
import Reports from "./pages/Reports";
import NotFound from "./pages/NotFound";

import HqDashboard from "./pages/hq/HqDashboard";
import DiscomDashboard from "./pages/discom/DiscomDashboard";
import AgencyDashboard from "./pages/agency/AgencyDashboard";
import ReconciliationWorkbench from "./pages/ReconciliationWorkbench";

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
          <Route path="/exceptions" element={<Exceptions />} />
          <Route path="/search" element={<Search />} />
          <Route path="/reports" element={<Reports />} />
        </Route>

        <Route path="/home" element={<Navigate to="/dashboard" replace />} />
        <Route path="*" element={<NotFound />} />

        <Route path="/reconciliation" element={<ReconciliationWorkbench />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;