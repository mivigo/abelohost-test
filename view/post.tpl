{extends file="layout.tpl"}

{block name=content}
<article class="container" style="margin-top: 40px; max-width: 900px;">
    <!-- Breadcrumbs -->
    <nav style="margin-bottom: 24px; font-size: 14px; color: var(--text-muted);">
        <a href="/" style="hover:color: var(--accent);">Главная</a> 
        {if !empty($categories)}
            &middot; <a href="/category/{$categories[0]->id}" style="hover:color: var(--accent);">{$categories[0]->name}</a>
        {/if}
        &middot; <span>Статья</span>
    </nav>

    <!-- Post Title -->
    <h1 style="font-size: 44px; line-height: 1.2; letter-spacing: -1px; margin-bottom: 20px; color: var(--text-primary);">
        {$post->name}
    </h1>

    <!-- Post Meta -->
    <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 32px; flex-wrap: wrap; font-size: 14px; color: var(--text-muted); border-bottom: 1px solid var(--border-color); padding-bottom: 24px;">
        <span style="display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {$post->created_at|date_format:"%e %B %Y"}
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            {$post->views} просмотров
        </span>
        <div style="display: flex; gap: 8px;">
            {foreach from=$categories item=cat}
                <a href="/category/{$cat->id}" style="background-color: var(--accent-light); color: var(--accent); font-weight: 600; padding: 4px 12px; border-radius: 30px; font-size: 12px;">
                    {$cat->name}
                </a>
            {/foreach}
        </div>
    </div>

    <!-- Post Image -->
    {if $post->img_path}
        <div style="border-radius: 24px; overflow: hidden; margin-bottom: 40px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
            <img src="{$post->img_path}" alt="{$post->name}" style="width: 100%; height: auto; display: block; max-height: 480px; object-fit: cover;">
        </div>
    {/if}

    <!-- Post Content Text -->
    <div style="font-size: 18px; line-height: 1.8; color: var(--text-primary); margin-bottom: 80px;">
        {$post->text|nl2br}
    </div>
</article>

<!-- Similar Posts Block -->
<section style="background-color: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 80px 0;">
    <div class="container" style="max-width: 900px;">
        <h2 style="font-size: 32px; margin-bottom: 40px; text-align: center;">Похожие статьи</h2>
        
        {if empty($similarPosts)}
            <p style="text-align: center; color: var(--text-muted); font-size: 15px;">Похожих статей пока нет.</p>
        {else}
            <div class="posts-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                {foreach from=$similarPosts item=simPost}
                    <article class="post-card" style="border-radius: 20px;">
                        <div class="post-image-container" style="height: 160px;">
                            {if $simPost->img_path}
                                <img src="{$simPost->img_path}" alt="{$simPost->name}" class="post-image" loading="lazy">
                            {else}
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#1e293b; color:#64748b;">No Image</div>
                            {/if}
                            <span class="post-meta-badge" style="top: 12px; right: 12px; font-size: 10px; padding: 4px 8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                {$simPost->views}
                            </span>
                        </div>
                        <div class="post-content" style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="post-date" style="font-size: 11px;">{$simPost->created_at|date_format:"%e %B %Y"}</span>
                            <h3 class="post-title" style="font-size: 16px; margin-bottom: 8px;">
                                <a href="/post/{$simPost->id}">{$simPost->name}</a>
                            </h3>
                            <p class="post-desc" style="font-size: 13px; line-height: 1.5; margin-bottom: 12px; flex-grow: 1;">{$simPost->description}</p>
                            <a href="/post/{$simPost->id}" class="read-more" style="font-size: 13px; padding-top: 12px; border-top: 1px solid var(--border-color); display: flex;">
                                Читать статью
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </article>
                {/foreach}
            </div>
        {/if}
    </div>
</section>
{/block}
