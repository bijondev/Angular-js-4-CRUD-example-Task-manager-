import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AppComponent } from './app.component';
import { CreatetaskComponent } from './task/createtask/createtask.component';
import { HttpModule } from '@angular/http';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './home/home/home.component';
import { DataServiceService } from './data-service.service';
import { EdittaskComponent } from './task/edittask/edittask.component';

const appRoutes: Routes=[
  {path:'', component:HomeComponent},
  {path:'newtask', component:CreatetaskComponent},
  {path:'edittask/:id', component:EdittaskComponent},
  // {path:'districts', component:DistrictsComponent},
  // {path:'new-districts', component:NewDistrictsComponent}
];
@NgModule({
  declarations: [
    AppComponent,
    CreatetaskComponent,
    HomeComponent,
    EdittaskComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    RouterModule.forRoot(appRoutes)
  ],
  providers: [DataServiceService],
  bootstrap: [AppComponent]
})
export class AppModule { }
