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
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

    angForm: FormGroup;
    submitted = false;
    success!: string;
    error!: string;
    hideSuccessMsg = true;

    private registerEventSubscription!: Subscription;

    constructor(private fb: FormBuilder, private dataService: ApiService, private router: Router) {
        this.angForm = this.fb.group({
          first_name: [null, [Validators.required, Validators.minLength(2)]],
          last_name: [null, [Validators.required, Validators.minLength(2)]], 
          email: [null, [Validators.required, Validators.minLength(1), Validators.email]],
          organization: [null, [Validators.required, Validators.minLength(4)]],
          phone: [null, [Validators.required, Validators.minLength(10), Validators.maxLength(10), Validators.pattern("^[0-9]*$")]],
        });
    }

    get f() { return this.angForm.controls; }

    ngOnInit(): void {
      sessionStorage.clear();
    }

    postdata(angForm1:any) {
      this.error = '';
      this.success = '';
      this.submitted = true;
      if (this.angForm.valid) {
        this.registerEventSubscription = this.dataService.register(angForm1.value.first_name, angForm1.value.last_name, angForm1.value.email, angForm1.value.phone, angForm1.value.organization)
          .pipe(first())
          .subscribe(
            data => {    
              this.error = '';
              if(data.status == 1){
                this.showSuccessToaster(data.message);
                this.navigateToOtp(data);
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
      if (this.registerEventSubscription) {
        this.registerEventSubscription.unsubscribe();
      }      
    }
  
    navigateToOtp(data:any){      
      this.router.navigate(['/otp'])
    }
  

}
