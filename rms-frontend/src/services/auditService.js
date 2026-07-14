import api from "./api";

export const getAuditLogs = (params = {}) =>
  api.get("/audit-logs", {
    params,
  });

export const getAuditSummary = () =>
  api.get("/audit-logs/summary");

export const getAuditFilters = () =>
  api.get("/audit-logs/filters");

export const getAuditLogDetails = (id) =>
  api.get(`/audit-logs/${id}`);