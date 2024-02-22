<template>
    <a-row :gutter="{ xs: 8, sm: 16, md: 24, lg: 32 }" class="course-container">
        <a-col :xs="24" :sm="24" :md="16" :lg="18" :xl="14">
            <a-card class="course-info-card">
                <a-typography>
                    <h1>{{ course.title }}</h1>
                    <p><strong>Learn:</strong> {{ course.learn }}</p>
                    <p><strong>Brief:</strong> {{ course.brief }}</p>
                    <p><strong>Description:</strong> {{ course.description }}</p>
                    <p><strong>Start On:</strong> {{ course.start_on }}</p>
                    <p><strong>Finish On:</strong> {{ course.finish_on }}</p>
                </a-typography>
                <a-alert v-if="!course.published" type="warning" message="This course is not yet published" banner
                    class="course-alert" />
                <slot />
            </a-card>
        </a-col>
        <a-col :xs="24" :sm="24" :md="8" :lg="6" :xl="4">
            <a-card title="Course Menu" class="course-menu-card">
                <a-menu mode="inline">
                    <a-menu-item key="files">
                        <inertia-link :href="route('manage.files.index')">Files</inertia-link>
                    </a-menu-item>
                    <a-menu-item key="pages">
                        <inertia-link :href="route('manage.pages.index')">Pages</inertia-link>
                    </a-menu-item>
                    <a-menu-item key="assignment">Assignment</a-menu-item>
                    <a-menu-item key="quiz">Quiz</a-menu-item>
                    <a-menu-item key="discussion">Discussion</a-menu-item>
                </a-menu>
            </a-card>
        </a-col>
    </a-row>
</template>

<script>
import { defineComponent } from 'vue';
import { Row, Col, Typography, Alert, Card, Menu } from 'ant-design-vue';

export default defineComponent({
    components: {
        'a-row': Row,
        'a-col': Col,
        'a-typography': Typography,
        'a-alert': Alert,
        'a-card': Card,
        'a-menu': Menu,
        'a-menu-item': Menu.Item,
    },
    props: ['course'],
    setup() {
        return {};
    },
    filters: {
        formatDate(value) {
            // Implement date formatting logic here, e.g., using moment.js
            return value;
        },
    },
});
</script>

<style scoped>
.course-container {
    margin-top: 20px;
}

.course-info-card,
.course-menu-card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    /* 添加轻微的阴影效果 */
}

.course-info h1 {
    font-size: 28px;
    /* 增大标题字体大小 */
    font-weight: 600;
    margin-bottom: 20px;
    /* 增加标题下方的间距 */
}

.course-info p {
    margin-bottom: 14px;
    /* 微调段落间距 */
    line-height: 1.7;
    /* 增加行高 */
    color: rgba(0, 0, 0, 0.65);
    /* 字体颜色使用深灰色，减少对眼睛的压力 */
}

.course-alert {
    margin-top: 20px;
    margin-bottom: 20px;
}

/* Google字体可以在HTML文档的<head>部分引入 */
/* 下面的代码应该移动到全局样式文件中 */
body {
    font-family: 'Roboto', sans-serif;
}

/* 添加媒体查询来进一步优化响应式设计 */
@media (max-width: 768px) {

    .course-info-card,
    .course-menu-card {
        margin-bottom: 20px;
    }
}</style>
