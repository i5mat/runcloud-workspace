<template>
  <div class="container">
    <div class="mt-1">
      <h4>Your Workspace</h4>
      <h6>Here we are going to list out all of the workspaces.</h6>

      <b-row v-if="workspaceList.length !== 0">
        <b-col
          v-for="wL in workspaceList"
          :key="wL.id"
          lg="6"
          class="mb-2"
          style="width: 100%"
        >
          <b-card
            bg-variant="dark"
            text-variant="white"
            :title="wL.workspaceName"
          >
            <b-card-text>
              Created on,<br />
              {{ wL.created_at }}
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>
      <b-row v-else>
        <b-col lg="6" class="mb-2" style="width: 100%">
          <b-card
            bg-variant="dark"
            text-variant="white"
            :title="'Create New Workspace'"
          >
            <b-card-text>
              Please create a new workspace, in order to display here.
            </b-card-text>
          </b-card>
        </b-col>
      </b-row>
    </div>

    <div class="mt-4">
      <h4 class="mt-3">Options</h4>
      <h6>Here are your options, feel free to choose over here.</h6>
    </div>

    <!-- MODAL CREATE WORKSPACE -->
    <b-modal
      id="modal-create-workspace"
      title="Create Workspace"
      centered
      size="md"
      hide-footer
      ref="my-modal"
    >
      <b-alert :variant="alertVariant" :show="showAlert">{{
        alertMsg
      }}</b-alert>
      <b-row>
        <b-col>
          <b-overlay
            :show="show"
            rounded="sm"
            spinner-variant="primary"
            spinner-type="grow"
          >
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
          </b-overlay>
        </b-col>
      </b-row>

      <b-button
        @click="submitData"
        :disabled="workspaceForm.name.length < 4"
        variant="primary"
        pill
        style="width: 100%"
        >Submit</b-button
      >
    </b-modal>

    <!-- MODAL CREATE TASK -->
    <b-modal
      id="modal-create-task"
      title="Create Task"
      centered
      size="lg"
      hide-footer
      ref="my-task-modal"
    >
      <b-overlay
        :show="show"
        rounded="sm"
        spinner-variant="primary"
        spinner-type="grow"
      >
        <b-alert show variant="success">
          <h4 class="alert-heading">Steps to create task</h4>
          <p>
            1. Choose one of the workspace that you have created.<br />
            2. Enter your desired task name, task start and end datetime. <br />
            3. Lastly, click submit and you're good to go!
          </p>
          <hr />
          <p class="mb-0">
            The button for submit will only be enabled if all of the fields is
            filled in.
          </p>
        </b-alert>
        <b-alert :variant="alertVariantTask" :show="showAlertTask">{{
          alertMsgTask
        }}</b-alert>
        <b-row>
          <b-col lg="6">
            <b-input-group size="md">
              <b-form-input
                id="filter-input"
                v-model="filterTask"
                type="search"
                placeholder="Type to Search"
              ></b-form-input>

              <b-input-group-append>
                <b-button
                  variant="primary"
                  :disabled="!filterTask"
                  @click="filterTask = ''"
                >
                  Clear
                </b-button>
              </b-input-group-append>
            </b-input-group>
          </b-col>

          <b-col class="text-right">
            <div id="tickets-table_length" class="dataTables_length">
              <label class="d-inline-flex align-items-center">
                Show&nbsp;
                <b-form-select
                  v-model="perPageTask"
                  size="md"
                  :options="pageOptionsTask"
                ></b-form-select
                >&nbsp;entries
              </label>
            </div>
          </b-col>
        </b-row>

        <b-table
          hover
          selectable
          show-empty
          @row-selected="onRowSelected"
          :select-mode="selectMode"
          :busy="isBusy"
          :items="workspaceList"
          :fields="fieldsWorkspace"
          :filter="filterTask"
          @filtered="onFilteredTask"
          :per-page="perPageTask"
          :current-page="currentPageTask"
        >
          <template #table-busy>
            <div class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
          </template>

          <template #cell(workspaceStatus)="data">
            <b-badge
              variant="success"
              v-if="data.item.workspaceStatus === 'ACTIVE'"
              >{{ data.item.workspaceStatus }}</b-badge
            >

            <b-badge variant="danger" v-else>{{
              data.item.workspaceStatus
            }}</b-badge>
          </template>
        </b-table>
        <b-pagination
          v-model="currentPageTask"
          :total-rows="totalRowsTask"
          :per-page="perPageTask"
          class="float-right"
        ></b-pagination>

        <h5 v-if="selected.length !== 0">
          You have selected: {{ selected[0].workspaceName }}
        </h5>
        <h5 v-else>You have selected: none</h5>
        <hr />
        <div class="accordion" role="tablist">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button block v-b-toggle.accordion-1 variant="info"
                >Click me to insert new task!</b-button
              >
            </b-card-header>
            <b-collapse
              id="accordion-1"
              accordion="my-accordion"
              role="tabpanel"
            >
              <b-card-body>
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
                      <b-form-datepicker
                        id="input-1"
                        v-model="taskForm.durationFromDate"
                        :min="min"
                        locale="en"
                        class="mb-2"
                      ></b-form-datepicker>
                    </b-form-group>
                  </b-col>
                  <b-col>
                    <b-form-group
                      id="fieldset-1"
                      label="Enter task start time"
                      label-for="input-1"
                    >
                      <b-time
                        v-model="taskForm.durationFromTime"
                        locale="en"
                      ></b-time>
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
                      <b-form-datepicker
                        id="input-1"
                        v-model="taskForm.durationToDate"
                        :min="taskForm.durationFromDate"
                        locale="en"
                        class="mb-2"
                      ></b-form-datepicker>
                    </b-form-group>
                  </b-col>
                  <b-col>
                    <b-form-group
                      id="fieldset-1"
                      label="Enter task end time"
                      label-for="input-1"
                    >
                      <b-time
                        v-model="taskForm.durationToTime"
                        locale="en"
                      ></b-time>
                    </b-form-group>
                  </b-col>
                </b-row>
                <b-button
                  @click="submitDataTask"
                  :disabled="selected.length === 0"
                  variant="primary"
                  style="width: 100%"
                  pill
                  >Submit</b-button
                >
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>
      </b-overlay>
    </b-modal>

    <!-- MODAL VIEW & UPDATE WORKSPACE -->
    <b-modal
      id="modal-view-data"
      title="View Workspace & Task"
      centered
      ok-only
      size="huge"
      ref="my-view-workspacetask-modal"
    >
      <b-overlay
        :show="showTasks"
        rounded="sm"
        spinner-variant="primary"
        spinner-type="grow"
      >
        <b-alert :variant="alertVariantUpdateTask" :show="showAlertUpdateTask">
          {{ alertMsgUpdateTask }}
        </b-alert>
        <b-row>
          <b-col lg="6">
            <b-input-group size="md">
              <b-form-input
                id="filter-input"
                v-model="filter"
                type="search"
                placeholder="Type to Search"
              ></b-form-input>

              <b-input-group-append>
                <b-button
                  variant="primary"
                  :disabled="!filter"
                  @click="filter = ''"
                >
                  Clear
                </b-button>
              </b-input-group-append>
            </b-input-group>
          </b-col>

          <b-col class="text-right">
            <div id="tickets-table_length" class="dataTables_length">
              <label class="d-inline-flex align-items-center">
                Show&nbsp;
                <b-form-select
                  v-model="perPage"
                  size="md"
                  :options="pageOptions"
                ></b-form-select
                >&nbsp;entries
              </label>
            </div>
          </b-col>
        </b-row>

        <b-table
          hover
          show-empty
          :busy="isBusy"
          :items="tasksList"
          :fields="fieldsTask"
          :filter="filter"
          @filtered="onFiltered"
          :per-page="perPage"
          :current-page="currentPage"
        >
          <template #table-busy>
            <div class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
          </template>

          <template #cell(taskStatus)="data">
            <b-badge
              variant="success"
              v-if="data.item.taskStatus === 'COMPLETED'"
              >{{ data.item.taskStatus }}</b-badge
            >

            <b-badge variant="danger" v-else>{{
              data.item.taskStatus
            }}</b-badge>
          </template>

          <template #cell(timeLeft)="data">
            <b-badge variant="warning" v-if="data.item.timeLeft === 'OVERDUE'"
              >{{ data.item.timeLeft }}
            </b-badge>
            <span v-else>
              {{ data.item.timeLeft }}
            </span>
          </template>

          <template #cell(action)="data">
            <b-form-checkbox
              v-model="checked[data.item.idx]"
              name="check-button"
              switch
              @change="snitched(data.item.id_task)"
              :disabled="tasksList[data.item.idx].taskStatus === 'COMPLETED'"
            >
              <!-- Switch Checkbox <b>(Checked: {{ checked }})</b> -->
            </b-form-checkbox>
          </template>
        </b-table>
        <b-pagination
          v-model="currentPage"
          :total-rows="totalRows"
          :per-page="perPage"
          class="float-right"
        ></b-pagination>

        <b-button
          @click="updateTask"
          variant="primary"
          :disabled="selectedVal.length === 0"
          >Submit</b-button
        >
      </b-overlay>
    </b-modal>

    <b-row>
      <!-- CREATE NEW WORKSPACE -->
      <b-col>
        <b-card
          :title="'Create Workspace'"
          :sub-title="'Currently, ' + workspaceList.length + ' workspaces.'"
          class="mb-2 h-100"
        >
          <b-card-text>
            Create your workspace here, it is compulsary to create a workspace
            before creating a task. Currently, you have
            {{ workspaceList.length }} workspaces.
          </b-card-text>

          <b-button v-b-modal.modal-create-workspace variant="primary"
            >Go</b-button
          >
        </b-card>
      </b-col>

      <!-- CREATE NEW TASK -->
      <b-col>
        <b-card
          :title="'Create Task'"
          :sub-title="'Currently, ' + tasksList.length + ' tasks created.'"
          class="mb-2 h-100"
        >
          <b-card-text>
            Create your task here. Currently, you have
            {{ tasksList.length }} tasks.
          </b-card-text>

          <b-button v-b-modal.modal-create-task variant="primary">Go</b-button>
        </b-card>
      </b-col>

      <!-- VIEW WORKSPACE AND TASK -->
      <b-col>
        <b-card
          title="View Workspace & Task"
          :sub-title="'View in-depth.'"
          class="mb-2 h-100"
        >
          <b-card-text>
            Currently you have, {{ workspaceList.length }} workspaces &
            {{ tasksList.length }} tasks.
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
      min: null,
      max: null,

      show: false,
      showTasks: false,

      totalRows: 1,
      filter: null,
      currentPage: 1,
      perPage: 3,
      pageOptions: [3, 25, 50, 100],

      totalRowsTask: 1,
      filterTask: null,
      currentPageTask: 1,
      perPageTask: 3,
      pageOptionsTask: [3, 25, 50, 100],

      checked: [],

      selectMode: "single",
      selected: [],
      isBusy: false,

      alertMsg: null,
      showAlert: false,
      alertVariant: null,

      alertMsgTask: null,
      showAlertTask: false,
      alertVariantTask: null,

      alertMsgUpdateTask: null,
      showAlertUpdateTask: false,
      alertVariantUpdateTask: null,
      workspaceForm: {
        name: "",
      },
      taskForm: {
        name: "",
        durationFromDate: "",
        durationFromTime: "",
        durationToDate: "",
        durationToTime: "",
        workspaceId: "",
        userId: "",
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
          label: "From",
          sortable: true,
        },
        {
          key: "taskDurationTo",
          label: "To",
          sortable: true,
        },
        {
          key: "taskStatus",
          label: "Status",
          sortable: true,
        },
        {
          key: "timeLeft",
        },
        {
          key: "taskCompletionDatetime",
          label: "Completed Date",
        },
        {
          key: "timeCompleted",
        },
        {
          key: "action",
          label: "Action",
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
      tasksList: [],
      selectedVal: [],
    };
  },
  async created() {
    await this.getWorkspaces();
    await this.getTasks();
    this.blockDate();

    // window.location.href = "https://rc-assessment.herokuapp.com/workspace-home";
    if (this.$router.history.current.path !== "/workspace-home") {
      window.location.href =
        "https://rc-assessment.herokuapp.com/workspace-home";
    }
  },
  methods: {
    blockDate() {
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const minDate = new Date(today);
      minDate.setMonth(minDate.getMonth());
      minDate.setDate(now.getDate());

      // this.max = minDate
      this.min = minDate;
    },
    snitched(val) {
      console.log(val);
      if (!this.selectedVal.includes(val)) this.selectedVal.push(val);
      else if (this.selectedVal.includes(val)) this.selectedVal.pop(val);
    },
    onFiltered(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    onFilteredTask(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRowsTask = filteredItems.length;
      this.currentPageTask = 1;
    },
    onRowSelected(items) {
      this.selected = items;
      this.taskForm.userId = parseInt(this.selected[0].userId);
      this.taskForm.workspaceId = this.selected[0].workspaceId;
    },
    format_date(value) {
      if (value) {
        return moment(String(value)).format("YYYY-MM-DD hh:mm A");
      }
    },
    async getWorkspaces() {
      this.workspaceList.length = 0;
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

          this.isBusy = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    async getTasks() {
      this.tasksList.length = 0;
      this.showTasks = true;
      await axios
        .get("/api/get-workspace-task-list")
        .then((res) => {
          console.log(res.data);
          var count = 0;
          for (let i = 0; i < res.data.length; i++) {
            this.checked.push(false);

            // var start = moment(new Date().toISOString().slice(0, 10), "YYYY-MM-DD")
            // var end = moment(res.data[i].taskDurationTo, "YYYY-MM-DD")

            // var startTime = moment(res.data[i].taskDurationFrom, "YYYY-MM-DD hh:mm:ss")
            var endTime = moment(
              res.data[i].taskDurationTo,
              "YYYY-MM-DD hh:mm:ss"
            );
            var timeCompleted = moment(
              res.data[i].taskCompletionDatetime,
              "YYYY-MM-DD hh:mm:ss"
            );

            var loaaa = moment().format("YYYY-MM-DD hh:mm:ss");
            if (moment.duration(endTime.diff(loaaa))._data.minutes < 0) {
              this.tasksList.push(
                Object.assign(
                  { idx: count++ },
                  { id_task: res.data[i].id },
                  { workspaceName: res.data[i].workspaceName },
                  { taskName: res.data[i].taskName },
                  {
                    taskDurationFrom: this.format_date(
                      res.data[i].taskDurationFrom
                    ),
                  },
                  {
                    taskDurationTo: this.format_date(
                      res.data[i].taskDurationTo
                    ),
                  },
                  { taskStatus: res.data[i].taskStatus },
                  {
                    taskCompletionDatetime: this.format_date(
                      res.data[i].taskCompletionDatetime
                    )
                      ? this.format_date(res.data[i].taskCompletionDatetime)
                      : "NO DATE",
                  },
                  {
                    timeLeft: "OVERDUE",
                  },
                  {
                    timeCompleted: "Cannot determine as it was overdue.",
                  }
                )
              );
            } else {
              this.tasksList.push(
                Object.assign(
                  { idx: count++ },
                  { id_task: res.data[i].id },
                  { workspaceName: res.data[i].workspaceName },
                  { taskName: res.data[i].taskName },
                  {
                    taskDurationFrom: this.format_date(
                      res.data[i].taskDurationFrom
                    ),
                  },
                  {
                    taskDurationTo: this.format_date(
                      res.data[i].taskDurationTo
                    ),
                  },
                  { taskStatus: res.data[i].taskStatus },
                  {
                    taskCompletionDatetime: this.format_date(
                      res.data[i].taskCompletionDatetime
                    )
                      ? this.format_date(res.data[i].taskCompletionDatetime)
                      : "NO DATE",
                  },
                  {
                    timeLeft:
                      moment.duration(endTime.diff(loaaa))._data.days +
                      " days, " +
                      moment.duration(endTime.diff(loaaa))._data.hours +
                      " hours, " +
                      moment.duration(endTime.diff(loaaa))._data.minutes +
                      " minutes remaining.",
                  },
                  {
                    timeCompleted:
                      moment.duration(timeCompleted.diff(loaaa))._data.days +
                      " days ago, " +
                      moment.duration(timeCompleted.diff(loaaa))._data.hours +
                      " hours ago, " +
                      Math.abs(
                        moment.duration(timeCompleted.diff(loaaa))._data.minutes
                      ) +
                      " minutes ago.",
                  }
                )
              );
            }

            if (this.tasksList[i].taskStatus === "INCOMPLETE") {
              this.checked[i] = false;
            } else {
              this.checked[i] = true;
            }
            this.totalRows = this.tasksList.length;
            this.totalRowsTask = this.workspaceList.length;
            this.showTasks = false;
          }

          // var start = moment(res.data[0].taskDurationFrom, "YYYY-MM-DD")
          // var end = moment(res.data[0].taskDurationTo, "YYYY-MM-DD")
          // console.log(moment.duration(end.diff(start)).asDays())

          // var startTime = moment(res.data[0].taskDurationFrom, "hh:mm:ss")
          // var endTime = moment(res.data[0].taskDurationTo, "hh:mm:ss")
          // console.log(moment.duration(endTime.diff(startTime)).asMinutes())

          this.isBusy = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    async submitData() {
      this.loading = true;
      this.show = true;
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
              this.isBusy = false;
              this.show = false;
              this.workspaceForm.name = "";
              this.$refs["my-modal"].hide();
              this.getWorkspaces();
            } else if (response.data[0] === "Failed") {
              this.alertVariant = "danger";
              this.showAlert = true;
              this.alertMsg = response.data[1];
              this.isBusy = false;
              this.show = false;
            }
          }.bind(this)
        )
        .catch((err) => {
          console.log(err);
        });
    },
    async submitDataTask() {
      this.loading = true;
      this.show = true;
      var data = JSON.stringify({
        task_from_date: this.taskForm.durationFromDate,
        task_from_time: this.taskForm.durationFromTime,
        task_to_date: this.taskForm.durationToDate,
        task_to_time: this.taskForm.durationToTime,
        task_name: this.taskForm.name,
        task_status: "INCOMPLETE",
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
              this.alertVariantTask = "success";
              this.showAlertTask = true;
              this.alertMsgTask = response.data[1];
              this.isBusy = false;
              this.$refs["my-task-modal"].hide();
              this.$refs["my-view-workspacetask-modal"].show();
              this.show = false;
              this.getTasks();
            } else if (response.data[0] === "Failed") {
              this.alertVariantTask = "danger";
              this.showAlertTask = true;
              this.alertMsgTask = response.data[1];
              this.isBusy = false;
              this.show = false;
            }
          }.bind(this)
        )
        .catch((err) => {
          console.log(err);
        });
    },
    async updateTask() {
      for (let z = 0; z < this.selectedVal.length; z++) {
        var data = JSON.stringify({
          task_status: "COMPLETED",
          task_completion_datetime: moment(new Date().toLocaleString()).format(
            "YYYY-MM-DD hh:mm:ss"
          ),
          test_hantar_stringify: JSON.stringify(this.selectedVal[z]),
        });

        var config = {
          method: "put",
          mode: "cors",
          url: `/update-workspace-task/` + this.selectedVal[z],
          headers: {
            "Content-type": "application/json",
          },
          data: data,
        };

        await axios(config)
          .then(
            function (response) {
              if (response.data[0] === "Success") {
                this.alertVariantUpdateTask = "success";
                this.showAlertUpdateTask = true;
                this.alertMsgUpdateTask = response.data[1];
                this.isBusy = false;
                this.getTasks();
              } else if (response.data[0] === "Failed") {
                this.alertVariantUpdateTask = "danger";
                this.showAlertUpdateTask = true;
                this.alertMsgUpdateTask = response.data[1];
                this.isBusy = false;
              }
            }.bind(this)
          )
          .catch((err) => {
            console.log(err);
          });
      }
      this.selectedVal.length = 0;
    },
  },
};
</script>

<style>
.modal .modal-huge {
  max-width: 1700px;
  width: 1700px;
}
</style>
