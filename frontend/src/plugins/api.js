import { LocalStorageService } from '@/services/LocalStorageService'
import api from "./axios";

export default {
  getHeader() {
    return { 
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: 'Bearer ' + LocalStorageService.getToken(),
      'Access-Control-Allow-Methods': '*',
      'Access-Control-Allow-Origin': '*'
    }
  },
  getFormDataHeader() {
    return {
      'Content-Type': 'multipart/form-data',
      Authorization: 'Bearer ' + LocalStorageService.getToken(),
      'Access-Control-Allow-Methods': '*',
      'Access-Control-Allow-Origin': '*'
    }
  },
  create(url, params) {
    return api.post(url, params, {headers: this.getHeader()})
  },
  download(url, data) {
    if (!data) data = {}
    return api.get(url, {params: data.params, headers: this.getHeader(), responseType: 'blob'})
  },
  get(url, data) {
    if (!data) data = {}
    return api.get(url, {params: data.params, headers: this.getHeader()})
  },
  update(url, params) {
    return api.put(url, params, {headers: this.getHeader()})
  },
  delete(url, params) {
    return api.delete(url, {params: params, headers: this.getHeader()})
  },
  methods: {
    
  }
}
