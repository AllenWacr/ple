<template>
  <OrganizationLayout title="Dashboard" :department="department">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">From Fields</h2>
    </template>
    <button
      @click="createRecord()"
      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3"
    >
      Create Field
    </button>

    <div class="ant-table">
      <div class="ant-table-container">
        <table style="table-layout: auto">
          <thead class="ant-table-thead">
            <tr>
              <th v-for="column in columns">{{ column.title }}</th>
              <th>Operation</th>
            </tr>
          </thead>
          <draggable
            tag="tbody"
            class="dragArea list-group ant-table-tbody"
            :list="fields"
            @change="rowChange"
          >
            <transition-group v-for="(record, idx) in fields">
              <tr class="ant-table-row ant-table-row-level-0" :key="record.id">
                <td v-for="column in columns" class="ant-table-cell">
                  {{ record[column.dataIndex] }}
                </td>
                <td>
                  <a-button @click="editRecord(record)">{{ $t("edit") }}</a-button>
                  <a-button
                    @click="deleteRecord(record)"
                    :disabled="form.published == 1"
                    >{{ $t("delete") }}</a-button
                  >
                </td>
              </tr>
            </transition-group>
          </draggable>
        </table>
      </div>
    </div>
    <p></p>

    <!-- Modal Start-->
    <a-modal
      v-model:visible="modal.isOpen"
      :title="modal.mode == 'CREATE' ? 'Create' : 'Update'"
      width="60%"
    >
      <a-form
        ref="modalRef"
        :model="modal.data"
        name="formField"
        :label-col="{ span: 4 }"
        :wrapper-col="{ span: 20 }"
        autocomplete="off"
        :rules="rules"
        :validate-messages="validateMessages"
      >
        <!-- <a-form-item label="Field name" name="field_name">
                <a-input v-model:value="modal.data.field_name" />
            </a-form-item> -->
        <a-form-item label="Field Label" name="field_label">
          <a-input v-model:value="modal.data.field_label" @blur="onFieldLabelChanged" />
        </a-form-item>
        <a-form-item label="Field Type" name="type">
          <a-select
            v-model:value="modal.data.type"
            placeholder="Field Type"
            :options="fieldTypes"
            @change="onChangeType"
          />
        </a-form-item>
        <a-form-item
          label="Rows"
          name="rows"
          v-if="['textarea', 'longtext', 'richtext'].includes(modal.data.type)"
        >
          <a-input-number v-model:value="modal.data.options" />
        </a-form-item>

        <template v-if="['radio', 'checkbox', 'dropdown'].includes(modal.data.type)">
          <a-form-item label="Options" name="options">
            <a-radio-group>
              <template v-for="option in modal.data.options">
                <a-radio :style="verticalStyle" :value="option.value">
                  <a-input v-model:value="option.label" />
                </a-radio>
              </template>
              <a-radio @click="addOptionItem"> Add option</a-radio>
            </a-radio-group>
          </a-form-item>
          <a-form-item label="Template" name="optionTemplate">
            <a-select :options="optionTemplates" @change="onChangeOptionTemplate" />
          </a-form-item>
          <a-form-item
            label="Template"
            name="optionTemplate"
            v-if="['radio', 'checkbox'].includes(modal.data.type)"
          >
            <a-radio-group v-model:value="modal.data.direction">
              <a-radio value="H">Horizontal</a-radio>
              <a-radio value="V">Vertical</a-radio>
            </a-radio-group>
          </a-form-item>
        </template>
        <a-form-item label="Compulsory" name="required">
          <a-switch
            v-model:checked="modal.data.required"
            :unCheckedValue="0"
            :checkedValue="1"
          />
        </a-form-item>
        <!-- <a-form-item label="規則" name="rule">
                <a-input v-model:value="modal.data.rule" />
            </a-form-item> -->
        <a-form-item label="Column data" name="in_column" v-if="modal.data.required">
          <a-switch
            v-model:checked="modal.data.in_column"
            :unCheckedValue="0"
            :checkedValue="1"
          />
        </a-form-item>
        <a-form-item label="Remark" name="remark">
          <a-textarea v-model:value="modal.data.remark" />
        </a-form-item>
      </a-form>
      <template #footer>
        <a-button
          v-if="modal.mode == 'EDIT'"
          key="Update"
          type="primary"
          @click="updateRecord()"
          >{{ $t("update") }}</a-button
        >
        <a-button
          v-if="modal.mode == 'CREATE'"
          key="Store"
          type="primary"
          @click="storeRecord()"
          >Create</a-button
        >
      </template>
    </a-modal>
    <!-- Modal End-->
  </OrganizationLayout>
</template>

<script>
import OrganizationLayout from "@/Layouts/OrganizationLayout.vue";
import { defineComponent, reactive } from "vue";
import { VueDraggableNext } from "vue-draggable-next";

export default {
  components: {
    OrganizationLayout,
    draggable: VueDraggableNext,
  },
  props: ["form", "fields"],
  data() {
    return {
      modal: {
        isOpen: false,
        data: {},
        title: "Modal",
        mode: "",
      },
      fieldTypes: [
        { value: "input", label: "單行文字" },
        { value: "textarea", label: "多行文字" },
        { value: "largetext", label: "長編文字" },
        { value: "richtext", label: "格式文字" },
        { value: "radio", label: "單項選擇" },
        { value: "checkbox", label: "多項選擇" },
        { value: "dropdown", label: "下拉清單" },
        { value: "true_false", label: "真/偽" },
        { value: "date", label: "日期格式" },
        { value: "datetime", label: "日期時間" },
        { value: "email", label: "電郵欄位" },
        { value: "number", label: "數值欄位" },
        { value: "photo", label: "上傳相片" },
      ],
      columns: [
        {
          title: "Field Label",
          dataIndex: "field_label",
        },
        {
          title: "Field Type",
          dataIndex: "type",
        },
        {
          title: "Compulsory",
          dataIndex: "required",
        },
        {
          title: "Column Data",
          dataIndex: "in_column",
        },
      ],
      rules: {
        field_label: { required: true },
        type: { required: true },
      },
      validateMessages: {
        required: "${label} is required!",
        types: {
          email: "${label} is not a valid email!",
          number: "${label} is not a valid number!",
        },
        number: {
          range: "${label} must be between ${min} and ${max}",
        },
      },
      labelCol: {
        style: {
          width: "150px",
        },
      },
      verticalStyle: {
        display: "flex",
        height: "30px",
        lineHeight: "30px",
        width: "100%",
      },
      optionTemplates: [
        {
          value: "agree",
          label: "Level of Agreement",
          template: [
            { value: 5, label: "Strongly Agree" },
            { value: 4, label: "Argree" },
            { value: 3, label: "Neutual" },
            { value: 2, label: "Disagree" },
            { value: 1, label: "Strongly Disagree" },
            { value: 0, label: "Not Applicable" },
          ],
        },
        {
          value: "gender",
          label: "Gender",
          template: [
            { value: "M", label: "Male" },
            { value: "F", label: "Female" },
          ],
        },
        {
          value: "clear",
          label: "Reset",
          template: [{ value: "option_1", label: "option_1" }],
        },
        {
          value: "belt",
          label: "Belt",
          template: [
            { value: "white", label: "白帶" },
            { value: "yellow", label: "黃帶" },
            { value: "orange", label: "橙帶" },
            { value: "green", label: "綠帶" },
            { value: "blue", label: "藍帶" },
            { value: "brown", label: "啡帶" },
            { value: "dan_1", label: "黑帶1段" },
            { value: "dan_2", label: "黑帶2段" },
            { value: "dan_3", label: "黑帶3段" },
            { value: "dan_4", label: "黑帶4段" },
            { value: "dan_5", label: "黑帶5段" },
            { value: "dan_6", label: "黑帶6段" },
            { value: "dan_7", label: "黑帶7段" },
            { value: "dan_8", label: "黑帶8段" },
            { value: "dan_9", label: "黑帶9段" },
            { value: "dan_10", label: "黑帶10段" },
          ],
        },
      ],
    };
  },
  created() {},
  methods: {
    createRecord() {
      this.modal.data = {};
      this.modal.data.form_id = this.form.id;
      this.modal.data.direction = "V";
      this.modal.mode = "CREATE";
      this.modal.isOpen = true;
    },
    editRecord(record) {
      this.modal.data = { ...record };
      try {
        this.modal.data.options = JSON.parse(this.modal.data.options);
      } catch (e) {
        this.modal.data.options = [{ value: "option_1", label: "option_1" }];
      }
      this.modal.mode = "EDIT";
      this.modal.isOpen = true;
    },
    storeRecord() {
      this.$refs.modalRef
        .validateFields()
        .then(() => {
          this.$inertia.post(
            route("manage.form.fields.store", {
              form: this.form.id,
            }),
            this.modal.data,
            {
              onSuccess: (page) => {
                this.modal.data = {};
                this.modal.isOpen = false;
              },
              onError: (err) => {
                console.log(err);
              },
            }
          );
        })
        .catch((err) => {
          console.log(err);
        });
    },
    updateRecord() {
      console.log(this.modal.data);
      this.$refs.modalRef
        .validateFields()
        .then(() => {
          this.$inertia.patch(
            route("manage.form.fields.update", {
              form: this.form.id,
              field: this.modal.data,
            }),
            this.modal.data,
            {
              onSuccess: (page) => {
                this.modal.data = {};
                this.modal.isOpen = false;
                console.log(page);
              },
              onError: (error) => {
                console.log(error);
              },
            }
          );
        })
        .catch((err) => {
          console.log("error", err);
        });
    },
    deleteRecord(record) {
      if (!confirm("Are you sure want to remove?")) return;
      this.$inertia.delete(
        route("form.fields.destroy", {
          form: this.form.id,
          field: record.id,
        }),
        {
          onSuccess: (page) => {
            console.log("the field has been deleted!");
          },
          onError: (error) => {
            alert(error.message);
          },
        }
      );
    },
    addOptionItem() {
      console.log(this.modal.data.options.length + 1);
      const newOption = "option_" + (this.modal.data.options.length + 1);
      this.modal.data.options.push({ value: newOption, label: newOption });
    },
    onChangeOptionTemplate(value) {
      this.modal.data.options = this.optionTemplates.find(
        (t) => t.value == value
      ).template;
    },
    onChangeType(value) {
      if (["textarea", "longtext", "richtext"].includes(this.modal.data.type)) {
        if (!Number.isInteger(this.modal.data.type)) {
          this.modal.data.options = 5;
        }
      } else if (["radio", "checkbox"].includes(this.modal.data.type)) {
        if (typeof this.modal.data.options !== "object") {
          this.modal.data.options = [{ value: "option_1", label: "option_1" }];
        }
      }
    },
    onFieldLabelChanged(value) {
      if (!this.modal.data.field_name) {
        this.modal.data.field_name = this.modal.data.field_label;
      }
    },
    rowChange(event) {
      var data = [];
      this.fields.forEach((field, idx) => {
        data.push({ id: field.id, form_id: field.form_id, sequence: idx });
      });
      console.log(data);
      this.$inertia.post(route("manage.form.fieldsSequence", this.form.id), data, {
        onSuccess: (page) => {
          console.log(page);
        },
        onError: (error) => {
          console.log(error);
        },
      });
    },
  },
  watch: {
    // "modal.data.type": function($value){
    //     if($value=='agree'){
    //         // if(this.modal.data.options=='null'){
    //             this.modal.data.options=[
    //                 {value:5,label:'非常同意'},
    //                 {value:4,label:'同意'},
    //                 {value:3,label:'普通'},
    //                 {value:2,label:'不同意'},
    //                 {value:1,label:'非常不同意'},
    //                 {value:0,label:'不適用'}
    //             ]
    //         //}
    //     }
    // }
  },
};
</script>
