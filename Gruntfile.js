module.exports = function(grunt) {
    grunt.initConfig({
        less: {
            development: {
                options: {
                    paths: ["html/less"],
                    yuicompress: true
                },
                files: {
                    "html/css/style.css": "html/less/style.less",
                    "html/css/admin/style.css": "html/less/admin/style.less"
                }
            }
        },
        watch: {
            files: "html/less/admin/*",
            tasks: ["less"]
        }
    });
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', 'watch'); // registrace defaultní úlohy
};