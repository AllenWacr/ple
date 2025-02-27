<template>
    <OrganizationLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $t("course") }}
            </h2>
        </template>

        <div>
            <div class="flex-auto pb-3 text-left">
                <a-button @click="goBack" type="dashed">Go Back</a-button>
            </div>
            <div class="flex-auto pb-3 text-right">
                <a-button @click="courseEditRecord" type="primary">Edit</a-button>
                <inertia-link :href="route('manage.course.contents.index', course.id)" class="ant-btn">{{
                    $t("View Contents") }}</inertia-link>
            </div>
        </div>

        <CourseBuilder :course="course">
            <div class="flex-auto pb-3 text-left">
                <a-button @click="moduleCreateRecord" type="primary">Create Module</a-button>
            </div>
            <a-collapse>
                <template v-for="(module, index) in course.modules" :key="`module-${index}`">
                    <a-collapse-panel>
                        <template #header>
                            <div class="module-header">
                                <span>{{ module.label }}</span>
                                <a-space>
                                    <a-button @click="moduleUpdateRecord(module)">Edit</a-button>
                                    <a-button type="danger" @click="moduleDeleteRecord(module)">Delete</a-button>
                                </a-space>
                            </div>
                        </template>
                        <ul class="module-list">
                            <template v-for="(content, index) in course.contents" :key="`content-${index}`">
                                <li v-if="content.module == module.value" class="module-list-item">
                                    <a-space class="center">
                                        {{ content.title }}
                                        <a-button @click="editRecord(content)" type="primary">Edit</a-button>
                                    </a-space>
                                </li>
                            </template>
                        </ul>
                    </a-collapse-panel>
                </template>
            </a-collapse>
            <a-collapse>
                <a-collapse-panel header="Individual">
                    <ul class="module-list">
                        <template v-for="(content, index) in individualContents" :key="`individual-content-${index}`">
                            <li class="module-list-item">{{ content.title }}</li>
                        </template>
                    </ul>
                </a-collapse-panel>
            </a-collapse>
        </CourseBuilder>

        <!-- Modal Start for Edit Course -->
        <a-modal v-model:visible="courseEditModal.isOpen" :title="$t(courseEditModal.title)" width="60%"
            :afterClose="courseEditModalClose" @ok="onCourseRecordUpdate" ok-text="Save">
            <a-form ref="modalRef" :model="courseEditModal.data" name="Certificate" :label-col="{ span: 8 }"
                :wrapper-col="{ span: 16 }" autocomplete="off" :rules="rules" :validate-messages="validateMessages"
                enctype="multipart/from-data">
                <a-form-item label="Title" name="title">
                    <a-input v-model:value="courseEditModal.data.title" />
                </a-form-item>
                <a-form-item label="Learn" name="learn">
                    <a-textarea v-model:value="courseEditModal.data.learn" />
                </a-form-item>
                <a-form-item label="Brief" name="brief">
                    <a-textarea v-model:value="courseEditModal.data.brief" />
                </a-form-item>
                <a-form-item label="Description" name="description">
                    <a-textarea v-model:value="courseEditModal.data.description" />
                </a-form-item>
                <a-form-item label="Image" name="image">
                    <a-input v-model:value="courseEditModal.data.image" />
                </a-form-item>
                <a-form-item label="Start on" name="start_on">
                    <a-date-picker v-model:value="courseEditModal.data.start_on" :valueFormat="dateFormat" />
                </a-form-item>
                <a-form-item label="Finish on" name="finish_on">
                    <a-date-picker v-model:value="courseEditModal.data.finish_on" :valueFormat="dateFormat" />
                </a-form-item>
                <a-form-item label="Published" name="published">
                    <a-switch v-model:checked="courseEditModal.data.published" />
                </a-form-item>
            </a-form>
        </a-modal>

        <!-- Modal Start for Create Module -->
        <a-modal v-model:visible="moduleCreateModal.isOpen" :title="$t(moduleCreateModal.title)" width="60%"
            :afterClose="moduleCreateModalClose" @ok="onRecordSave" ok-text="Save">
            <a-form ref="modalFormRef" :model="moduleCreateModal.data" name="Certificate" :label-col="{ span: 8 }"
                :wrapper-col="{ span: 16 }" autocomplete="off" :rules="rules" :validate-messages="validateMessages"
                enctype="multipart/form-data">
                <a-form-item label="Module Name" name="module_name">
                    <a-input v-model:value="moduleCreateModal.data.module_name" />
                </a-form-item>
            </a-form>
        </a-modal>
        <!-- Modal End -->

        <!-- Modal Start for Edit Module -->
        <a-modal v-model:visible="moduleEditModal.isOpen" :title="$t(moduleEditModal.title)" width="60%"
            :afterClose="moduleEditModalClose" @ok="onRecordUpdate" ok-text="Update">
            <a-form ref="modalRef" :model="moduleEditModal.data" name="Certificate" :label-col="{ span: 8 }"
                :wrapper-col="{ span: 16 }" autocomplete="off" :rules="rules" :validate-messages="validateMessages"
                enctype="multipart/form-data">
                <a-form-item label="Module Name" name="label">
                    <a-input v-model:value="moduleEditModal.data.label" />
                </a-form-item>
            </a-form>
        </a-modal>
        <!-- Modal End -->

        <!-- Modal Start for Delete Module -->
        <a-modal v-model:visible="moduleDeleteModal.isOpen" :title="$t(moduleDeleteModal.title)" width="60%"
            :afterClose="moduleDeleteModalClose" @ok="onRecordDelete" ok-text="Confirm">
            <a-form ref="modalRef" :model="moduleDeleteModal.data" name="Certificate" :label-col="{ span: 8 }"
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
import CourseBuilder from "@/Components/Organization/CourseBuilder.vue";
import {
    UploadOutlined,
    LoadingOutlined,
    PlusOutlined,
    InfoCircleFilled,
DingdingOutlined,
} from "@ant-design/icons-vue";
import { defineComponent, reactive } from "vue";
import CropperModal from "@/Components/Member/CropperModal.vue";

export default {
    components: {
        OrganizationLayout,
        UploadOutlined,
        LoadingOutlined,
        PlusOutlined,
        InfoCircleFilled,
        CropperModal,
        CourseBuilder
    },
    props: ["course", 'content_types'],
    data() {
        return {
            open: false,
            individualContents: [],
            dateFormat: 'YYYY-MM-DD',
            courseEditModal: {
                isOpen: false,
                data: {},
                title: "Edit Course",
                mode: "",
            },
            moduleCreateModal: {
                isOpen: false,
                data: {},
                title: "Create Module",
                mode: "",
            },
            moduleEditModal: {
                isOpen: false,
                data: {},
                title: "Edit Module",
                mode: "",
            },
            moduleDeleteModal: {
                isOpen: false,
                data: {},
                title: "Delete Module",
                mode: "",
            },
            rules: {
                category_code: { required: true },
                cert_title: { required: true },

                module_name: { required: true },
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
        };
    },
    created() { },
    mounted() {
        const modules = this.course.modules.map(m => m.value)
        console.log(modules)
        this.individualContents = this.course.contents.filter(c => !modules.includes(c.module))
        console.log(this.individualContents);
    },
    methods: {
        goBack() {
            window.history.back();
        },
        courseEditRecord() {
            this.courseEditModal.data = {
                title: this.course.title, learn: this.course.learn, brief: this.course.brief,
                description: this.course.description, image: this.course.image, start_on: this.course.start_on,
                finish_on: this.course.finish_on, published: this.course.published? true : false }
            this.courseEditModal.mode = "UPDATE";
            this.courseEditModal.isOpen = true;
            this.moduleCreateModal.isOpen = false;
            this.moduleEditModal.isOpen = false;
            this.moduleDeleteModal.isOpen = false;
        },
        courseEditModalClose() {
            console.log('Close edit course modal.');
        },
        onCourseRecordUpdate(e) {
            console.log(e);
            console.log(this.courseEditModal.data)
            this.$refs.modalRef.validateFields().then(() => {
                this.$inertia.patch(route('manage.courses.update', this.course.id), this.courseEditModal.data, {
                    onSuccess: (page) => {
                        console.log(page);
                        this.courseEditModal.isOpen = false;
                    },
                    onError: (err) => {
                        console.log(err);
                    }
                });
            })
        },
        moduleCreateRecord() {
            this.moduleCreateModal.data = {};
            this.moduleCreateModal.mode = "CREATE";
            this.moduleCreateModal.isOpen = true;
            this.moduleEditModal.isOpen = false;
            this.moduleDeleteModal.isOpen = false;
        },
        moduleCreateModalClose() {
            console.log('Close create module modal.');
        },
        onRecordSave(e) {
            console.log(e);
            console.log(this.moduleCreateModal.data)
            this.$refs.modalRef.validateFields().then(() => {
                this.$inertia.post(route('manage.course.createModule', this.course.id), this.moduleCreateModal.data, {
                    onSuccess: (page) => {
                        console.log(page);
                        this.moduleCreateModal.isOpen = false;
                    },
                    onError: (err) => {
                        console.log(err);
                    }
                });
            })
        },
        editRecord(content) {
            console.log(content);
            this.$inertia.visit(route('manage.course.contents.edit', { course: content.course_id, content: content.id }));
        },
        moduleUpdateRecord(module) {
            this.moduleEditModal.data = { value: module.value, label: module.label };
            this.moduleEditModal.mode = "UPDATE";
            this.moduleEditModal.isOpen = true;
            this.moduleCreateModal.isOpen = false;
            this.moduleDeleteModal.isOpen = false;
        },
        moduleEditModalClose() {
            console.log('Close edit module modal.');
        },
        onRecordUpdate(e) {
            console.log(e);
            console.log(this.moduleEditModal.data)
            this.$refs.modalRef.validateFields().then(() => {
                this.$inertia.patch(route('manage.course.updateModule', this.course.id), this.moduleEditModal.data, {
                    onSuccess: (page) => {
                        console.log(page);
                        this.moduleEditModal.isOpen = false;
                    },
                    onError: (err) => {
                        console.log(err);
                    }
                });
            })
        },
        moduleDeleteRecord(module) {
            this.moduleDeleteModal.data = { value: module.value, label: module.label };
            this.moduleDeleteModal.mode = "DELETE";
            this.moduleDeleteModal.isOpen = true;
            this.moduleCreateModal.isOpen = false;
            this.moduleEditModal.isOpen = false;
        },
        moduleDeleteModalClose() {
            console.log('Close delete module modal.');
        },
        onRecordDelete(e) {
            console.log(e);
            console.log(this.moduleDeleteModal.data)
            this.$refs.modalRef.validateFields().then(() => {
                this.$inertia.post(route('manage.course.destroyModule', this.course.id), this.moduleDeleteModal.data, {
                    onSuccess: (page) => {
                        console.log(page);
                        this.moduleDeleteModal.isOpen = false;
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

<style>
.ant-collapse-content>.ant-collapse-content-box {
    padding: 0 !important
}

.module-list-item {
    padding-left: 10px;
    border-bottom: 1px solid lightgray;
}

.module-list {
    line-height: 30px;
}
</style>
<style scoped>
.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.module-list {
    list-style-type: none;
    padding: 0;
}

.module-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e8e8e8;
    padding: 8px 0;
}
</style>
