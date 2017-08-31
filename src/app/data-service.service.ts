import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/map';

let apiUrl = 'http://bmsolutionsbd.com/aj4crud/';

@Injectable()
export class DataServiceService {

  constructor(public http: Http) { }
  postData(credentials, type){
    
        return new Promise((resolve, reject) =>{
          let headers = new Headers();
          this.http.post(apiUrl+type, JSON.stringify(credentials), {headers: headers}).
          subscribe(res =>{
            resolve(res.json());
          }, (err) =>{
            reject(err);
          });
    
        });
    
      }
  getData(type){
    return new Promise((resolve, reject) =>{
      let headers = new Headers();
      this.http.get(apiUrl+type).
      subscribe(res =>{
        resolve(res.json());
      }, (err) =>{
        reject(err);
      });

    });
  }
}
