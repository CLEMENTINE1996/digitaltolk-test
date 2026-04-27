import axios from 'axios'

const HEADERS = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization' : null,
    "Access-Control-Allow-Methods": "*",
    "Access-Control-Allow-Origin": "*"
  }

export default axios.create({
  baseURL: import.meta.env.VITE_API_ROOT,
  withCredentials: false,
  headers: HEADERS,
  timeout: 1000000
 })