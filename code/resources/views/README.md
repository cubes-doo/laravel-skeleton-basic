## View Scripts Naming Convention

View scripts should follow controller/action naming convention in SNAKE CASE format.

Every controller should have its folder for its view scripts based on its name, ex:
    EntitiesController->all --->  entities/all.blade.php
    EntitiesController->edit --->  entities/edit.blade.php
    
If Controller is namespaced then viewscripts should be placed in subfolder, ex
    Admin\BlogPostsController->all -> admin/blog_posts/all.blade.php

Partial scripts should be placed in "partials" folder
    entities/partials/datatable_actions.blade.php

Layout and application wide partials should be placed in "_layout" folder ( see resources/views/_layout/README.md).