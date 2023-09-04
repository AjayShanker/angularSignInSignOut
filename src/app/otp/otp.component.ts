import { Component, OnInit, OnDestroy } from '@angular/core';
import { FormGroup, FormControl, FormBuilder, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { Router } from '@angular/router';
import { ApiService } from '../api.service';
import { Subscription } from 'rxjs';

declare const successNotify: any;
declare const errorNotify: any;
declare const warningNotify: any;
declare const infoNotify: any;

@Component({
  selector: 'app-otp',
  templateUrl: './otp.component.html',
  styleUrls: ['./otp.component.css']
})
export class OtpComponent implements OnInit {

    angForm: FormGroup;
    submitted = false;
    success!: string;
    userId!: any;
    error!: string;
    hideSuccessMsg = true;

    private otpEventSubscription!: Subscription;

    constructor(private fb: FormBuilder, private dataService: ApiService, private router: Router) {
        this.angForm = this.fb.group({
          user_input_otp: [null, [Validators.required, Validators.minLength(6), Validators.maxLength(6), Validators.pattern("^[0-9]*$")]],
        });
    }

    get f() { return this.angForm.controls; }

  ngOnInit(): void {
          if (sessionStorage.getItem("USER_ID")=="" || sessionStorage.getItem("USER_ID")===null)
            {
              this.router.navigate(['/register'])
            }
  }

  postdata(angForm1:any) {
    this.error = '';
    this.success = '';
    this.submitted = true;
    if (this.angForm.valid) {
      this.userId = sessionStorage.getItem("USER_ID");
      this.otpEventSubscription = this.dataService.otp_verification(this.userId, angForm1.value.user_input_otp)
        .pipe(first())
        .subscribe(
          data => {    
            this.error = '';
            if(data.status == 1){
              this.showSuccessToaster(data.message);
              this.navigateToMeeting();
            }else{
              this.error = data.message;  
              this.showErrorToaster(data.message);
            }              
            this.onReset();
            this.hideSuccessMsg = false;
            this.FadeOutSuccessMsg();         
          },
          error => {
            // Reset the form
            this.onReset();
            this.error = 'Some error occured. Please try again.';
            this.showWarningToaster(this.error);
            this.hideSuccessMsg = false;
            this.FadeOutSuccessMsg();
          });
    }

  }

  showSuccessToaster(message: string) {
    successNotify("Success!", message);
  }

  showErrorToaster(message: string) {
    errorNotify("Error!", message);
  }

  showWarningToaster(message: string) {
    warningNotify("Warning!", message);
  }

  showInfoToaster(message: string) {
    infoNotify("Info!", message);
  }


  onReset() {
    this.submitted = false;
    this.angForm.reset();
  }

  FadeOutSuccessMsg() {
    setTimeout(() => {
      this.hideSuccessMsg = true;
    }, 4000);
  }

  ngOnDestroy() {
    if (this.otpEventSubscription) {
      this.otpEventSubscription.unsubscribe();
    }      
  }

  navigateToMeeting(){      
    this.router.navigate(['/meeting'])
  }

}
