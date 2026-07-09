import api from "./api";

export const getExceptions = () => api.get("/exceptions");

export const getExceptionById = (id) => api.get(`/exceptions/${id}`);

export const updateException = (id, payload) =>
  api.put(`/exceptions/${id}`, payload);

export const assignException = (id, payload) =>
  api.post(`/exceptions/${id}/assign`, payload);

export const resolveException = (id, payload) =>
  api.post(`/exceptions/${id}/resolve`, payload);

export const verifyException = (id) =>
  api.post(`/exceptions/${id}/verify`);

export const closeException = (id) =>
  api.post(`/exceptions/${id}/close`);

export const reopenException = (id) =>
  api.post(`/exceptions/${id}/reopen`);