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
    <b-modal id="modal-create-task" title="Create Task" centered size="xl">
      <b-table
        striped
        hover
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
      </b-table>
    </b-modal>

    <!-- MODAL VIEW WORKSPACE -->
    <b-modal
      id="modal-view-data"
      title="View Workspace & Task"
      centered
      size="xl"
    >
      <b-table striped hover :items="items" :fields="fields"></b-table>
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
    state() {
      return this.workspaceForm.name.length >= 4;
    },
    invalidFeedback() {
      if (this.workspaceForm.name.length > 0) {
        return "Enter at least 4 characters.";
      }
      return "Please enter something.";
    },
  },
  data() {
    return {
      isBusy: false,
      alertMsg: null,
      showAlert: false,
      alertVariant: null,
      workspaceForm: {
        name: "",
      },
      fieldsWorkspace: [
        {
          key: "workspaceName",
          sortable: true,
        },
        {
          key: "workspaceStatus",
          sortable: false,
        },
        {
          key: "created_at",
          label: "Created On",
          sortable: true,
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
    };
  },
  async created() {
    await this.getWorkspaces();
  },
  methods: {
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
  },
};
</script>

<style>
</style>
