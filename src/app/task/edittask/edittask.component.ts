import { Component, OnInit } from '@angular/core';
import { DataServiceService } from './../../data-service.service';
import {Router, ActivatedRoute, Params} from '@angular/router';
@Component({
  selector: 'app-edittask',
  templateUrl: './edittask.component.html',
  styleUrls: ['./edittask.component.css']
})
export class EdittaskComponent implements OnInit {
  taskData = {
    "id":"",
    "taskname": "",
    "description": "",
    "status": ""
  };
  public resposeData : any;
  constructor(private route: ActivatedRoute, public DataServiceService : DataServiceService, private activatedRoute: ActivatedRoute) { 
    this.activatedRoute.params.subscribe((params: Params) => {
      let id = params['id'];
      this.get_task_by_id(id);
      console.log(id);
    });

  }

  ngOnInit() {
  }
  get_task_by_id(id){
    this.taskData.id=id;
    this.DataServiceService.postData(this.taskData, "get_task_by_id").then((result) =>{
      this.resposeData = result;
      this.taskData=this.resposeData.taskData[0];
      console.log(this.taskData);
      // location.href = '/';
      }, (err) => {
        //Connection failed message
      });
   }
  updatetask(){
    console.log(this.taskData);
    if(this.taskData.taskname && this.taskData.description){
      //Api connections
      this.DataServiceService.postData(this.taskData, "updatetask").then((result) =>{
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
