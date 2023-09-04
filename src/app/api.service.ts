import { Injectable, Output, EventEmitter } from '@angular/core';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Users } from './users';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

    redirectUrl: string = '';
    baseUrl: string = "http://localhost/meetingScheduleAPI"; 
    users: Users[] = [];
    data: any = [];
    @Output() getLoggedInName: EventEmitter<any> = new EventEmitter();

    constructor(private httpClient: HttpClient) { }

    public register(first_name='', last_name='', email='', phone='', organization='') {     
        return this.httpClient.post<any>(this.baseUrl + '/register.php', { first_name, last_name, email, phone, organization }).pipe(
          map((res) => {
            this.data = res;
            if(res.status==1){
              this.setOTPEntryUserDetails(res.Id, res.first_name, res.last_name, res.email, res.phone, res.organization);
            }
            return this.data;
          }),
          catchError(this.handleError));
    }

    public login(email='', password='') {
        return this.httpClient.post<any>(this.baseUrl + '/login.php', { email, password })
        .pipe(map(res => {
          if(res.status==1){
            this.setUserName(res.name);
            this.setToken(res.email);
            this.setUserID(res.id);
            this.getLoggedInName.emit(true);
          }        
          return res;
        }),
        catchError(this.handleError));
    }

    public otp_verification(user_id='', user_input_otp='') {
      return this.httpClient.post<any>(this.baseUrl + '/otpverification.php', { user_id, user_input_otp })
      .pipe(map(res => {
        if(res.status==1){
          this.setUserName(res.name);
          this.setToken(res.email);
          this.setUserID(res.id);
          this.getLoggedInName.emit(true);
        }        
        return res;
      }),
      catchError(this.handleError));
  }

    private handleError(error: HttpErrorResponse) {
      console.log(error);
      return throwError('Error! something went wrong.');
    }
  
    //token
    setToken(token: string) {
      sessionStorage.setItem('email', token);
    }
  
    setUserID(token: string) {
      sessionStorage.setItem('log_id', token);
    }  
  
    getToken() {
      return sessionStorage.getItem('email');
    }
  
    deleteToken() {
      sessionStorage.removeItem('name');
      sessionStorage.removeItem('email');
      sessionStorage.removeItem('log_id');
      sessionStorage.removeItem('picture');
    }

    setUserName(name:string) {
      sessionStorage.setItem('name', name);
    }

    setOTPEntryUserDetails(Id:string, first_name:string, last_name:string, email:string, phone:string, organization:string){
      sessionStorage.setItem('USER_ID',Id);
      sessionStorage.setItem('USER_FIRST_NAME',first_name);
      sessionStorage.setItem('USER_LAST_NAME',last_name);
      sessionStorage.setItem('USER_EMAIL',email);
      sessionStorage.setItem('USER_PHONE',phone);
      sessionStorage.setItem('USER_COMPANY',organization);
      return true;
    }
    
    isLoggedIn() {
      const usertoken = this.getToken();
      if (usertoken != null) {
        return true
      }
      return false;
    }


}
