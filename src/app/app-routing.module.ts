import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { RegisterComponent } from './register/register.component';
import { LoginComponent } from './login/login.component';
import { OtpComponent } from './otp/otp.component';
import { MeetingComponent } from './meeting/meeting.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { NotFoundComponent } from './not-found/not-found.component';

import { AuthguardGuard } from './authguard.guard';

const routes: Routes = [
  { path: '', component: RegisterComponent, pathMatch: 'full' },
  { path: 'otp', component: OtpComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'dashboard', component: DashboardComponent, canActivate: [AuthguardGuard]},
  { path: 'meeting', component: MeetingComponent, canActivate: [AuthguardGuard]},
  { path: '**', component: NotFoundComponent, canActivate: [AuthguardGuard]}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
