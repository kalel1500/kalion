```php

    public function __construct(
        private UserRepositoryContract $repositoryUser,
    )
    {
    }

    public function test(): void
    {
//        dd(userEntity()->can('see_post_detail'));

        $user1 = $this->repositoryUser->find(1);
        $user2 = $this->repositoryUser->find(2);
        $user3 = $this->repositoryUser->find(3);
        $user4 = $this->repositoryUser->find(4);
//        dump($user->toArray());
        dd($user1, $user1->toArray());

        $user1_can_see_post_detail = $user1->can('see_post_detail');
        $user1_can_filter_posts    = $user1->can('filter_posts');
        $user1_can_see_tags        = $user1->can('see_tags');
        $user1_can_admin_tags      = $user1->can('admin_tags');

        $user2_can_see_post_detail = $user2->can('see_post_detail');
        $user2_can_filter_posts    = $user2->can('filter_posts');
        $user2_can_see_tags        = $user2->can('see_tags');
        $user2_can_admin_tags      = $user2->can('admin_tags');

        $user3_can_see_post_detail = $user3->can('see_post_detail');
        $user3_can_filter_posts    = $user3->can('filter_posts');
        $user3_can_see_tags        = $user3->can('see_tags');
        $user3_can_admin_tags      = $user3->can('admin_tags');

        $user4_can_see_post_detail = $user4->can('see_post_detail');
        $user4_can_filter_posts    = $user4->can('filter_posts');
        $user4_can_see_tags        = $user4->can('see_tags');
        $user4_can_admin_tags      = $user4->can('admin_tags');

        dump([
            'user1_can_see_post_detail' => $user1_can_see_post_detail,
            'user1_can_filter_posts'    => $user1_can_filter_posts,
            'user1_can_see_tags'        => $user1_can_see_tags,
            'user1_can_admin_tags'      => $user1_can_admin_tags,
            'user2_can_see_post_detail' => $user2_can_see_post_detail,
            'user2_can_filter_posts'    => $user2_can_filter_posts,
            'user2_can_see_tags'        => $user2_can_see_tags,
            'user2_can_admin_tags'      => $user2_can_admin_tags,
            'user3_can_see_post_detail' => $user3_can_see_post_detail,
            'user3_can_filter_posts'    => $user3_can_filter_posts,
            'user3_can_see_tags'        => $user3_can_see_tags,
            'user3_can_admin_tags'      => $user3_can_admin_tags,
            'user4_can_see_post_detail' => $user4_can_see_post_detail,
            'user4_can_filter_posts'    => $user4_can_filter_posts,
            'user4_can_see_tags'        => $user4_can_see_tags,
            'user4_can_admin_tags'      => $user4_can_admin_tags,
        ]);

        $user4_can_admin_tags      = $user4->can('edit_cplan', $ystemIds, $centerIds);
        dd('fin');
    }
    
    
    public function examplePermissions()
    {
        // ONE
        // userEntity()->can('edit_roles');
        // userEntity()->is('admin');

        // ARRAY
        $array_userCan_seePostDetail_or_AdminTags = user()?->can(['see_post_detail', 'admin_tags']);
        $array_userIs_admin_or_reader             = user()?->is(['admin', 'reader']);

        // PIPE
        $pipe_userCan_seePostDetail_or_AdminTags = user()?->can('see_post_detail|admin_tags');
        $pipe_userIs_admin_or_reader             = user()?->is('admin|reader');

        dump([
            'user'                                     => user()?->toArray(),
            'array_userCan_seePostDetail_or_AdminTags' => $array_userCan_seePostDetail_or_AdminTags,
            'array_userIs_admin_or_reader'             => $array_userIs_admin_or_reader,

            'pipe_userCan_seePostDetail_or_AdminTags' => $pipe_userCan_seePostDetail_or_AdminTags,
            'pipe_userIs_admin_or_reader'             => $pipe_userIs_admin_or_reader,
        ]);
        
        
        // -------------------------------------------------------------------------------
        
        
        $systems = [1, 2, 3];
        $groups  = [4, 5, 6];
        $centers = [7, 8, 9];
        $imp_systems = implode(',', $systems);
        $imp_groups  = implode(',', $groups);
        $imp_centers = implode(',', $centers);

        user()->can("admin_tags:$imp_systems;$imp_groups;aaaa;1|see_post_detail:$imp_centers|filter_posts");
        user()->can('admin_tags|see_post_detail|filter_posts', [$systems, $groups, 'aaaa', 1], $centers);
        user()->can(['admin_tags', 'see_post_detail', 'filter_posts'], [$systems, $groups, 'aaaa', 1], $centers);

        user()->is("admin:$imp_systems;$imp_groups;aaaa;1|writer:$imp_centers|reader");
        user()->is('admin|writer|reader', [$systems, $groups, 'aaaa', 1], $centers);
        user()->is(['admin', 'writer', 'reader'], [$systems, $groups, 'aaaa', 1], $centers);
        
        // ------------------------------------------------------------------------------- 

        user()->can('see_post_detail|admin_tags:25,null');
        user()->can('see_post_detail|admin_tags', null, 25);
        user()->can(['see_post_detail', 'admin_tags'], [null], [25, null]);

        user()->is('admin|is_important_group:25');
        user()->is('admin|is_important_group', null, 25);
        user()->is(['admin', 'is_important_group'], [null], [25]);

        dd('fin');
    }

```



```php

Route::middleware(['userCan:see_post_detail|admin_tags:25'])->group(function () {
    Route::get('/home', [DefaultController::class, 'home'])->name('home');
});




        $id = (int) $request->input('id', '1');
        $repo = new UserRepository();
        $user = $repo->find($id);

        $user_can1 = $user->can('see_post_detail|admin_tags:25');
        $user_can2 = $user->can('see_post_detail|admin_tags', null, 25);
        $user_can3 = $user->can(['see_post_detail', 'admin_tags'], [null], [25, null]);

        $user_is1 = $user->is('admin|is_important_group:25');
        $user_is2 = $user->is('admin|is_important_group', null, 25);
        $user_is3 = $user->is(['admin', 'is_important_group'], [null], [25]);

        $data = [
            "user_can('see_post_detail|admin_tags:25,null')"                  => $user_can1,
            "user_can('see_post_detail|admin_tags', null, 25)"                => $user_can2,
            "user_can(['see_post_detail', 'admin_tags'], [null], [25, null])" => $user_can3,
            "----" => '----',
            "user_is('admin|is_important_group:25')"                          => $user_is1,
            "user_is('admin|is_important_group', null, 25)"                   => $user_is2,
            "user_is(['admin', 'is_important_group'], [null], [25])"          => $user_is3,
        ];

        dd($data);



interface UserRepositoryContract
{
    public function is_important_group(UserEntity $user, $id): bool;
}


final class UserRepository extends BaseUserRepository implements UserRepositoryContract
{
    public function is_important_group(UserEntity $user, $id): bool
    {
        return $user->id->value() === 4 && $id === 25;
    }
}
```