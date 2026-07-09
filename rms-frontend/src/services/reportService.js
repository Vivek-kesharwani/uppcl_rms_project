import api from "./api";

export const getDailyReport = () => api.get("/reports/daily-reconciliation");
export const getExceptionReport = () => api.get("/reports/exception-summary");
export const getSettlementReport = () => api.get("/reports/settlement-summary");