import { Component, OnInit } from '@angular/core';
import { DataServiceService } from './../../data-service.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  public resposeData : any;
  taskData = {
    "id": "",
    "taskname": "",
    "taskdesc": "",
    "status": ""
  };
  constructor(public DataServiceService : DataServiceService) {

    this.getAlltask();
  }
  ngOnInit() {
  }
  getAlltask(){
    console.log(this.taskData);
      //Api connections
      this.DataServiceService.getData( "alltask").then((result) =>{
        this.resposeData = result;
        console.log(this.resposeData);
    
        }, (err) => {
          //Connection failed message
        });
  }
  deletTask(id){
    this.taskData.id=id;
    var r = confirm("COnfirm Delete?");
    if (r == true) {
      this.DataServiceService.postData(this.taskData, "deletetask").then((result) =>{
        this.resposeData = result;
        console.log(this.resposeData);
        location.href = '/';
        }, (err) => {
          //Connection failed message
        });
    } else {
      // location.href = '/';
    }

}
}