<template>
  <div class="container">
    <!-- MODAL CREATE WORKSPACE -->
    <b-modal
      id="modal-create-workspace"
      title="Create Workspace"
      centered
      size="md"
      hide-footer
    >
      <b-alert :variant="alertVariant" :show="showAlert">{{
        alertMsg
      }}</b-alert>
      <b-row>
        <b-col>
          <b-form-group
            id="fieldset-1"
            label="Enter your workspace name"
            label-for="input-1"
            valid-feedback="Thank you!"
            :invalid-feedback="invalidFeedback"
            :state="state"
          >
            <b-form-input
              id="input-1"
              v-model="workspaceForm.name"
              :state="state"
              trim
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>

      <b-button @click="submitData" :disabled="workspaceForm.name.length < 4"
        >Submit</b-button
      >
    </b-modal>

    <!-- MODAL CREATE TASK -->
    <b-modal id="modal-create-task" title="Create Task" centered size="xl" hide-footer>
      <b-alert :variant="alertVariant" :show="showAlert">{{ alertMsg }}</b-alert>
      <b-table
        hover
        selectable
        @row-selected="onRowSelected"
        :select-mode="selectMode"
        :busy="isBusy"
        :items="workspaceList"
        :fields="fieldsWorkspace"
      >
        <template #table-busy>
            <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
            </div>
        </template>

        <template #cell(workspaceStatus)="data">
            <b-badge variant="success" v-if="data.item.workspaceStatus === 'ACTIVE'">{{ data.item.workspaceStatus }}</b-badge>

            <b-badge variant="danger" v-else>{{ data.item.workspaceStatus }}</b-badge>
        </template>
      </b-table>
      <h4 v-if="selected.length !== 0">
          You have selected: {{ selected[0].workspaceName }}
      </h4>
      <h4 v-else>
          You have selected: none
      </h4>
      <hr>
      <b-row>
        <b-col>
          <b-form-group
            id="fieldset-1"
            label="Enter your task name"
            label-for="input-1"
            valid-feedback="Thank you!"
            :invalid-feedback="invalidFeedbackTaskName"
            :state="stateTaskName"
          >
            <b-form-input
              id="input-1"
              v-model="taskForm.name"
              :state="stateTaskName"
              trim
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>

      <b-row>
        <b-col>
          <b-form-group
            id="fieldset-1"
            label="Enter task start date"
            label-for="input-1"
          >
            <b-form-datepicker id="input-1" v-model="taskForm.durationFromDate" class="mb-2"></b-form-datepicker>
          </b-form-group>
        </b-col>
        <b-col>
          <b-form-group
            id="fieldset-1"
            label="Enter task start time"
            label-for="input-1"
          >
            <b-time v-model="taskForm.durationFromTime" locale="en"></b-time>
          </b-form-group>
        </b-col>
      </b-row>

      <b-row>
        <b-col>
          <b-form-group
            id="fieldset-1"
            label="Enter task end date"
            label-for="input-1"
          >
            <b-form-datepicker id="input-1" v-model="taskForm.durationToDate" class="mb-2"></b-form-datepicker>
          </b-form-group>
        </b-col>
        <b-col>
          <b-form-group
            id="fieldset-1"
            label="Enter task end time"
            label-for="input-1"
          >
            <b-time v-model="taskForm.durationToTime" locale="en"></b-time>
          </b-form-group>
        </b-col>

      </b-row>
      <b-button @click="submitDataTask" :disabled="selected.length === 0"
        >Submit</b-button
      >
    </b-modal>

    <!-- MODAL VIEW WORKSPACE -->
    <b-modal
      id="modal-view-data"
      title="View Workspace & Task"
      centered
      size="xl"
    >
      <b-table
        hover
        :busy="isBusy"
        :items="tasksList"
        :fields="fieldsTask"
      >
        <template #table-busy>
            <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
            </div>
        </template>

        <template #cell(taskStatus)="data">
            <b-badge variant="success" v-if="data.item.taskStatus === 'ACTIVE'">{{ data.item.taskStatus }}</b-badge>

            <b-badge variant="danger" v-else>{{ data.item.taskStatus }}</b-badge>
        </template>
      </b-table>
    </b-modal>

    <b-row>
      <!-- CREATE NEW WORKSPACE -->
      <b-col>
        <b-card
          title="Create Workspace"
          img-src="https://picsum.photos/600/300/?image=20"
          img-alt="Image"
          img-top
          tag="article"
          class="mb-2"
        >
          <b-card-text>
            Some quick example text to build on the card title and make up the
            bulk of the card's content.
          </b-card-text>

          <b-button v-b-modal.modal-create-workspace variant="primary"
            >Go</b-button
          >
        </b-card>
      </b-col>

      <!-- CREATE NEW TASK -->
      <b-col>
        <b-card
          title="Create Task"
          img-src="https://picsum.photos/600/300/?image=20"
          img-alt="Image"
          img-top
          tag="article"
          class="mb-2"
        >
          <b-card-text>
            Some quick example text to build on the card title and make up the
            bulk of the card's content.
          </b-card-text>

          <b-button v-b-modal.modal-create-task variant="primary">Go</b-button>
        </b-card>
      </b-col>

      <!-- VIEW WORKSPACE AND TASK -->
      <b-col>
        <b-card
          title="View Workspace/Task"
          img-src="https://picsum.photos/600/300/?image=20"
          img-alt="Image"
          img-top
          tag="article"
          class="mb-2"
        >
          <b-card-text>
            Some quick example text to build on the card title and make up the
            bulk of the card's content.
          </b-card-text>

          <b-button v-b-modal.modal-view-data variant="primary">Go</b-button>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import moment from "moment";
export default {
  computed: {
    // FOR WORKSPACE FORM
    state() {
      return this.workspaceForm.name.length >= 4;
    },
    invalidFeedback() {
      if (this.workspaceForm.name.length > 0) {
        return "Enter at least 4 characters.";
      }
      return "Please enter something.";
    },

    // FOR TASK FORM
    stateTaskName() {
      return this.taskForm.name.length >= 4;
    },
    invalidFeedbackTaskName() {
      if (this.taskForm.name.length > 0) {
        return "Enter at least 4 characters.";
      }
      return "Please enter something.";
    },
  },
  data() {
    return {
      selectMode: 'single',
      selected: [],
      isBusy: false,
      alertMsg: null,
      showAlert: false,
      alertVariant: null,
      workspaceForm: {
        name: "",
      },
      taskForm: {
        name: '',
        durationFromDate: '',
        durationFromTime: '',
        durationToDate: '',
        durationToTime: '',
        workspaceId: '',
        userId: ''
      },
      fieldsWorkspace: [
        {
          key: "workspaceName",
          sortable: true,
        },
        {
          key: "created_at",
          label: "Created On",
          sortable: true,
        },
        {
          key: "workspaceStatus",
          sortable: false,
        },
      ],
      fieldsTask: [
        {
          key: "workspaceName",
          sortable: true,
        },
        {
          key: "taskName",
          sortable: false,
        },
        {
          key: "taskDurationFrom",
          sortable: true,
        },
        {
          key: "taskDurationTo",
          sortable: true,
        },
        {
          key: "taskStatus",
          sortable: true,
        },
        {
          key: "timeLeft"
        },
      ],
      fields: [
        {
          key: "last_name",
          sortable: true,
        },
        {
          key: "first_name",
          sortable: false,
        },
        {
          key: "age",
          label: "Person age",
          sortable: true,
        },
      ],
      items: [
        {
          isActive: true,
          age: 40,
          first_name: "Dickerson",
          last_name: "Macdonald",
        },
        { isActive: false, age: 21, first_name: "Larsen", last_name: "Shaw" },
        { isActive: false, age: 89, first_name: "Geneva", last_name: "Wilson" },
        { isActive: true, age: 38, first_name: "Jami", last_name: "Carney" },
      ],
      workspaceList: [],
      tasksList: []
    };
  },
  async created() {
    await this.getWorkspaces();
    await this.getTasks();
  },
  methods: {
    onRowSelected(items) {
      this.selected = items
      this.taskForm.userId = parseInt(this.selected[0].userId)
      this.taskForm.workspaceId = this.selected[0].workspaceId
    },
    format_date(value) {
      if (value) {
        return moment(String(value)).format("YYYY-MM-DD hh:mm A");
      }
    },
    async getWorkspaces() {
      this.workspaceList.length = 0
      await axios
        .get("/api/get-workspace-list")
        .then((res) => {
          for (let i = 0; i < res.data.length; i++) {
            this.workspaceList.push(
              Object.assign(
                { userId: res.data[i].workspace_userId },
                { workspaceId: res.data[i].id },
                { workspaceName: res.data[i].workspaceName },
                { workspaceStatus: res.data[i].workspaceStatus },
                { created_at: this.format_date(res.data[i].created_at) }
              )
            );
          }

          this.isBusy = false
        })
        .catch((error) => {
          console.log(error);
        });
    },
    async getTasks() {
      this.tasksList.length = 0
      await axios
        .get("/api/get-workspace-task-list")
        .then((res) => {
          console.log(res.data)
          for (let i = 0; i < res.data.length; i++) {
            // var start = moment(new Date().toISOString().slice(0, 10), "YYYY-MM-DD")
            // var end = moment(res.data[i].taskDurationTo, "YYYY-MM-DD")

            // var startTime = moment(res.data[i].taskDurationFrom, "YYYY-MM-DD hh:mm:ss")
            var endTime = moment(res.data[i].taskDurationTo, "YYYY-MM-DD hh:mm:ss")

            var loaaa = moment().format("YYYY-MM-DD hh:mm:ss")
            console.log(moment.duration(endTime.diff(loaaa)))

            this.tasksList.push(
              Object.assign(
                { workspaceName: res.data[i].workspaceName },
                { taskName: res.data[i].taskName },
                { taskDurationFrom: this.format_date(res.data[i].taskDurationFrom) },
                { taskDurationTo: this.format_date(res.data[i].taskDurationTo) },
                { taskStatus: res.data[i].taskStatus },
                { timeLeft: moment.duration(endTime.diff(loaaa))._data.days + ' days, ' + moment.duration(endTime.diff(loaaa))._data.minutes + ' minutes remaining.' }
              )
            );
          }

        // var start = moment(res.data[0].taskDurationFrom, "YYYY-MM-DD")
        // var end = moment(res.data[0].taskDurationTo, "YYYY-MM-DD")
        // console.log(moment.duration(end.diff(start)).asDays())

        // var startTime = moment(res.data[0].taskDurationFrom, "hh:mm:ss")
        // var endTime = moment(res.data[0].taskDurationTo, "hh:mm:ss")
        // console.log(moment.duration(endTime.diff(startTime)).asMinutes())

          this.isBusy = false
        })
        .catch((error) => {
          console.log(error);
        });
    },
    async submitData() {
      this.loading = true;
      var data = JSON.stringify({
        workspace_name: this.workspaceForm.name,
        workspace_status: "ACTIVE",
      });

      var config = {
        method: "post",
        mode: "cors",
        url: `/insert-workspace`,
        headers: {
          "Content-type": "application/json",
        },
        data: data,
      };

      await axios(config)
        .then(
          function (response) {
            if (response.data[0] === "Success") {
              this.alertVariant = "success";
              this.showAlert = true;
              this.alertMsg = response.data[1];
              this.isBusy = false
              this.getWorkspaces();
            } else if (response.data[0] === "Failed") {
              this.alertVariant = "danger";
              this.showAlert = true;
              this.alertMsg = response.data[1];
              this.isBusy = false
            }
          }.bind(this)
        )
        .catch((err) => {
          console.log(err);
        });
    },
    async submitDataTask() {
      this.loading = true;
      var data = JSON.stringify({
        task_from_date: this.taskForm.durationFromDate,
        task_from_time: this.taskForm.durationFromTime,
        task_to_date: this.taskForm.durationToDate,
        task_to_time: this.taskForm.durationToTime,
        task_name: this.taskForm.name,
        task_status: "ACTIVE",
        userid: this.taskForm.userId,
        workspaceid: this.taskForm.workspaceId,
      });

      var config = {
        method: "post",
        mode: "cors",
        url: `/insert-workspace-task`,
        headers: {
          "Content-type": "application/json",
        },
        data: data,
      };

      await axios(config)
        .then(
          function (response) {
            if (response.data[0] === "Success") {
              this.alertVariant = "success";
              this.showAlert = true;
              this.alertMsg = response.data[1];
              this.isBusy = false
            //   this.getWorkspaces();
            } else if (response.data[0] === "Failed") {
              this.alertVariant = "danger";
              this.showAlert = true;
              this.alertMsg = response.data[1];
              this.isBusy = false
            }
          }.bind(this)
        )
        .catch((err) => {
          console.log(err);
        });
    },
  },
};
</script>

<style>
</style>
