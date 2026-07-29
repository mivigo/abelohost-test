{extends file="layout.tpl"}

{block name=content}
<div class="container" style="margin-top: 40px;">
    <!-- Breadcrumbs -->
    <nav style="margin-bottom: 20px; font-size: 14px; color: var(--text-muted);">
        <a href="/" style="hover:color: var(--accent);">Главная</a> &middot; <span>Категория</span>
    </nav>

    <!-- Category Header -->
    <div class="category-header" style="border: none; margin-bottom: 40px; padding-bottom: 0;">
        <div class="category-info">
            <h1 style="font-size: 40px; margin-bottom: 12px;">{$category->name}</h1>
            <p style="font-size: 18px; color: var(--text-secondary); max-width: 800px;">{$category->description}</p>
        </div>
    </div>

    <!-- Controls Bar (Sorting & Stats) -->
    <div style="display: flex; justify-content: space-between; align-items: center; background-color: var(--bg-secondary); border: 1px solid var(--border-color); padding: 16px 24px; border-radius: 16px; margin-bottom: 40px; flex-wrap: wrap; gap: 16px;">
        <div style="font-size: 14px; color: var(--text-muted);">
            Всего статей в категории: <strong style="color: var(--text-primary);">{$totalPosts}</strong>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;">
            <span style="color: var(--text-muted);">Сортировка:</span>
            <a href="/category/{$category->id}?sort=date&order=desc&page=1" class="btn {if $sortBy !== 'date'}btn-secondary{/if}" style="padding: 8px 16px; font-size: 13px; border-radius: 8px;">
                По дате
            </a>
            <a href="/category/{$category->id}?sort=views&order=desc&page=1" class="btn {if $sortBy !== 'views'}btn-secondary{/if}" style="padding: 8px 16px; font-size: 13px; border-radius: 8px;">
                По просмотрам
            </a>
        </div>
    </div>

    <!-- Posts Grid -->
    {if empty($posts)}
        <div style="text-align: center; padding: 80px 0; background-color: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 24px;">
            <h3 style="margin-bottom: 8px; color: var(--text-primary);">Статьи не найдены</h3>
            <p style="color: var(--text-muted);">В этой категории пока нет опубликованных постов.</p>
        </div>
    {else}
        <div class="posts-grid">
            {foreach from=$posts item=post}
                <article class="post-card">
                    <div class="post-image-container">
                        {if $post->img_path}
                            <img src="{$post->img_path}" alt="{$post->name}" class="post-image" loading="lazy">
                        {else}
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#1e293b; color:#64748b;">No Image</div>
                        {/if}
                        <span class="post-meta-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            {$post->views}
                        </span>
                    </div>
                    <div class="post-content">
                        <span class="post-date">{$post->created_at|date_format:"%e %B %Y"}</span>
                        <h3 class="post-title">
                            <a href="/post/{$post->id}">{$post->name}</a>
                        </h3>
                        <p class="post-desc">{$post->description}</p>
                        
                        <div class="post-footer">
                            <a href="/post/{$post->id}" class="read-more">
                                Читать полностью
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
            {/foreach}
        </div>

        <!-- Pagination Section -->
        {if $totalPages > 1}
            <div style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 60px;">
                <!-- Previous Button -->
                {if $page > 1}
                    <a href="/category/{$category->id}?page={$page-1}&sort={$sortBy}&order={$sortOrder}" class="btn btn-secondary" style="padding: 10px 16px; border-radius: 12px; display: inline-flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    </a>
                {/if}

                <!-- Page Numbers -->
                {for $i=1 to $totalPages}
                    <a href="/category/{$category->id}?page={$i}&sort={$sortBy}&order={$sortOrder}" class="btn {if $page !== $i}btn-secondary{/if}" style="padding: 10px 18px; border-radius: 12px; font-weight: 600;">
                        {$i}
                    </a>
                {/for}

                <!-- Next Button -->
                {if $page < $totalPages}
                    <a href="/category/{$category->id}?page={$page+1}&sort={$sortBy}&order={$sortOrder}" class="btn btn-secondary" style="padding: 10px 16px; border-radius: 12px; display: inline-flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                {/if}
            </div>
        {/if}
    {/if}
</div>
{/block}
