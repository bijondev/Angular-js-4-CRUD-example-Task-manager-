import { Component, OnInit } from '@angular/core';
import { DataServiceService } from './../../data-service.service';
@Component({
  selector: 'app-createtask',
  templateUrl: './createtask.component.html',
  styleUrls: ['./createtask.component.css']
})
export class CreatetaskComponent implements OnInit {

  public resposeData : any;
 
  taskData = {
    "taskname": "",
    "taskdesc": "",
    "status": ""
  };

  constructor(public DataServiceService : DataServiceService) {

   }

  ngOnInit() {
  }

  createtask(){
    console.log(this.taskData);
    if(this.taskData.taskname && this.taskData.taskdesc){
      //Api connections
      this.DataServiceService.postData(this.taskData, "createtask").then((result) =>{
        this.resposeData = result;
        console.log(this.resposeData);
        location.href = '/';
        }, (err) => {
          //Connection failed message
        });
      }
  else{

   }
  
  }
}
