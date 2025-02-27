<template>
    <OrganizationLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $t("courses") }}
            </h2>
        </template>

        <div class="flex-auto pb-3 text-right">
            <a-button @click="createRecord" type="primary">Create Course</a-button>
        </div>

        <div class="container mx-auto pt-5">
            <div class="bg-white relative shadow rounded-lg overflow-x-auto">
                <a-table :dataSource="courses" :columns="columns">
                    <template #headerCell="{ column }">
                        {{ column.i18n ? $t(column.i18n) : column.title }}
                    </template>
                    <template #bodyCell="{ column, text, record, index }">
                        <template v-if="column.dataIndex === 'operation'">
                            <a-space>
                                <inertia-link :href="route('manage.courses.edit', record.id)" class="ant-btn">
                                    <a-icon :component="icons.EditOutlined" />
                                    <span>{{ $t("Detail") }}</span>
                                </inertia-link>
                                <inertia-link :href="route('manage.course.contents.index', record.id)" class="ant-btn">
                                    <a-icon :component="icons.EyeOutlined" />
                                    <span>{{ $t("View contents") }}</span>
                                </inertia-link>
                                <a-button @click="deleteRecord(record.id)" type="danger">
                                    <a-icon :component="icons.DeleteOutlined" />
                                    <span>Delete</span>
                                </a-button>
                            </a-space>
                        </template>
                        <template v-else-if="column.dataIndex === 'teachers'">
                            <span v-for="(teacher, index) in record.teachers" :key="index">
                                {{ teacher.name }}<span v-if="index < record.teachers.length - 1">, </span>
                            </span>
                        </template>
                        <template v-else>
                            {{ text }}
                        </template>
                    </template>
                </a-table>
            </div>
        </div>

        <!-- Modal Start for Create Course -->
        <a-modal v-model:visible="createModal.isOpen" :title="$t(createModal.title)" width="60%"
            :afterClose="createModalClose" @ok="onRecordSave" ok-text="Save">
            <a-form ref="modalRef" :model="createModal.data" name="Certificate" :label-col="{ span: 8 }"
                :wrapper-col="{ span: 16 }" autocomplete="off" :rules="rules" :validate-messages="validateMessages"
                enctype="multipart/form-data">
                <a-form-item label="Title" name="title">
                    <a-input v-model:value="createModal.data.title" />
                </a-form-item>
                <a-form-item label="Learn" name="learn">
                    <a-textarea v-model:value="createModal.data.learn" />
                </a-form-item>
                <a-form-item label="Brief" name="brief">
                    <a-textarea v-model:value="createModal.data.brief" />
                </a-form-item>
                <a-form-item label="Description" name="description">
                    <a-textarea v-model:value="createModal.data.description" />
                </a-form-item>
                <a-form-item label="Image" name="image">
                    <a-input v-model:value="createModal.data.image" />
                </a-form-item>
                <a-form-item label="Start on" name="start_on">
                    <a-date-picker v-model:value="createModal.data.start_on" :valueFormat="dateFormat" />
                </a-form-item>
                <a-form-item label="Finish on" name="finish_on">
                    <a-date-picker v-model:value="createModal.data.finish_on" :valueFormat="dateFormat" />
                </a-form-item>
                <a-form-item label="Published" name="published">
                    <a-switch v-model:checked="createModal.data.published" />
                </a-form-item>
            </a-form>
        </a-modal>
        <!-- Modal End -->

        <!-- Modal Start for Delete Course -->
        <a-modal v-model:visible="deleteModal.isOpen" :title="$t(deleteModal.title)" width="60%"
            :afterClose="deleteModalClose" @ok="onRecordDelete" ok-text="Confirm">
            <a-form ref="modalRef" :model="deleteModal.data" name="Certificate" :label-col="{ span: 8 }"
                :wrapper-col="{ span: 16 }" autocomplete="off" :rules="rules" :validate-messages="validateMessages"
                enctype="multipart/form-data">
                <p>Are you sure you want to delete this course?</p>
            </a-form>
        </a-modal>
        <!-- Modal End -->
    </OrganizationLayout>
</template>

<script>
import OrganizationLayout from "@/Layouts/OrganizationLayout.vue";
import {
    UploadOutlined,
    LoadingOutlined,
    PlusOutlined,
    InfoCircleFilled,
} from "@ant-design/icons-vue";
import CropperModal from "@/Components/Member/CropperModal.vue";
import { EditOutlined, EyeOutlined, DeleteOutlined } from '@ant-design/icons-vue';
export default {
    components: {
        OrganizationLayout,
        UploadOutlined,
        LoadingOutlined,
        PlusOutlined,
        InfoCircleFilled,
        CropperModal,
        EditOutlined,
        EyeOutlined,
        DeleteOutlined,
    },
    props: ["courses"],
    data() {
        return {
            dateFormat: 'YYYY-MM-DD',
            createModal: {
                isOpen: false,
                data: {},
                title: "Create Course",
                mode: "",
            },
            deleteModal: {
                isOpen: false,
                data: {},
                title: "Delete Course",
                mode: "",
            },
            columns: [
                {
                    title: "Title",
                    i18n: "course_title",
                    dataIndex: "title",
                }, {
                    title: "Teachers",
                    i18n: "course_teachers",
                    dataIndex: "teachers",
                }, {
                    title: "Operation",
                    i18n: "operation",
                    dataIndex: "operation",
                }
            ],
            rules: {
                title: { required: true },
                start_on: { required: true },
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
            icons: {
                EditOutlined,
                EyeOutlined,
                DeleteOutlined
            },
        };
    },
    created() { },
    methods: {
        createRecord() {
            this.createModal.data = {};
            this.createModal.mode = "CREATE";
            this.createModal.isOpen = true;
            this.deleteModal.isOpen = false;
        },
        createModalClose() {
            console.log('Close create modal');
        },
        deleteRecord(courseId) {
            this.deleteModal.data = {};
            this.deleteModal.mode = "DELETE";
            this.deleteModal.isOpen = true;
            this.createModal.isOpen = false;
            this.deleteModal.data.courseId = courseId;
        },
        deleteModalClose() {
            console.log('Close delete modal');
        },
        onRecordSave(e) {
            console.log(e);
            console.log(this.createModal.data)
            this.$refs.modalRef.validateFields().then(() => {
                this.$inertia.post(route('manage.courses.store'), this.createModal.data, {
                    onSuccess: (page) => {
                        console.log(page);
                        this.createModal.isOpen = false;
                    },
                    onError: (err) => {
                        console.log(err);
                    }
                });
            })
        },
        onRecordDelete(e) {
            console.log(e);
            console.log(this.deleteModal.data)
            this.$refs.modalRef.validateFields().then(() => {
                this.$inertia.delete(route('manage.courses.destroy', this.deleteModal.data.courseId), {
                    onSuccess: (page) => {
                        console.log(page);
                        this.deleteModal.isOpen = false;
                    },
                    onError: (err) => {
                        console.log(err);
                    }
                });
            })
        }
    }
};

</script>
