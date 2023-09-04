import { Component, OnInit, OnDestroy } from '@angular/core';
import { FormGroup, FormControl, FormBuilder, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { Router } from '@angular/router';
import { ApiService } from '../api.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

    angForm: FormGroup;
    submitted = false;
    success!: string;
    error!: string;
    hideSuccessMsg = true;

    private loginEventSubscription!: Subscription;

    constructor(private fb: FormBuilder, private dataService: ApiService, private router: Router) {
        this.angForm = this.fb.group({
          email: [null, [Validators.required, Validators.minLength(1), Validators.email]],
          password: [null, [Validators.required, Validators.minLength(6)]],
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
      this.loginEventSubscription = this.dataService.login(angForm1.value.email, angForm1.value.password)
        .pipe(first())
        .subscribe(
          data => {    
            this.error = '';
            console.log(data);
            if(data.status == 1){
              this.showSuccessToaster(data.message);
              const redirect = this.dataService.redirectUrl ? this.dataService.redirectUrl : '/home';
              this.router.navigate([redirect]);
            }else  if(data.status == 0){
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
            this.error = 'Invalid Credentials. Please try again.';
            this.showWarningToaster(this.error);
            this.hideSuccessMsg = false;
            this.FadeOutSuccessMsg();
          });
    }

  }

  showSuccessToaster(message: string) {
    //this.toastr.show(message, "Success!");
    //successNotify("Success!", message);
  }

  showErrorToaster(message: string) {
    //this.toastr.show(message, "Error!");
    //errorNotify("Error!", message);
  }

  showWarningToaster(message: string) {
    //this.toastr.show(message, "Warning!");
    //warningNotify("Warning!", message);
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
    if (this.loginEventSubscription) {
      this.loginEventSubscription.unsubscribe();
    }      
  }

  navigateToRegister(){
    this.router.navigate(['/register'])
  }

}
