<?php
/**
 * APPOLIOS - Admin Categories Management
 */

$adminSidebarActive = 'categories';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            
            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>Categories</h1>
                        <p>Manage course categories with course types</p>
                    </div>
                </div>

                <?php if (isset($flash) && !empty($flash)): ?>
                    <?php foreach ($flash as $type => $message): ?>
                        <div style="background: <?= $type === 'error' ? '#fee2e2' : '#dcfce7' ?>; color: <?= $type === 'error' ? '#991b1b' : '#166534' ?>; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Add New Category -->
                <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #e5e7eb;">
                    <h3 style="margin: 0 0 1rem 0; color: #1f2937;">Add New Category</h3>
                    <form action="<?= APP_ENTRY ?>?url=admin/store-category" method="POST" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Category Name</label>
                            <input type="text" name="name" placeholder="e.g., Programming" required style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem;">
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Description</label>
                            <input type="text" name="description" placeholder="Description (optional)" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem;">
                        </div>
                        <div style="flex: 2; min-width: 300px;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Course Types (comma separated)</label>
                            <input type="text" name="types" placeholder="e.g., Python, JavaScript, React, Django" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem;">
                        </div>
                        <button type="submit" style="background: #3b82f6; color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Add Category</button>
                    </form>
                </div>

                <!-- Categories List -->
                <div style="background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">ID</th>
                                <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Name</th>
                                <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Description</th>
                                <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Course Types</th>
                                <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Courses</th>
                                <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 1rem; color: #6b7280;"><?= $category['id'] ?></td>
                                        <td style="padding: 1rem; font-weight: 600; color: #1f2937;"><?= htmlspecialchars($category['name']) ?></td>
                                        <td style="padding: 1rem; color: #64748b;"><?= htmlspecialchars($category['description'] ?? '') ?></td>
                                        <td style="padding: 1rem; max-width: 250px;">
                                            <?php if (!empty($category['types'])): ?>
                                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                    <?php foreach (explode(',', $category['types']) as $type): ?>
                                                        <span style="background: #e0f2f1; color: #2B4865; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;"><?= htmlspecialchars(trim($type)) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #9ca3af; font-size: 0.85rem;">No types</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php 
                                            $categoryModel = new Category();
                                            $count = $categoryModel->getCoursesCount($category['id']);
                                            ?>
                                            <span style="background: #e0f2f1; color: #2B4865; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                                <?= $count ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <a href="<?= APP_ENTRY ?>?url=admin/delete-category/<?= $category['id'] ?>" 
                                               onclick="return confirm('Are you sure?')"
                                               style="background: #ef4444; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 3rem; color: #6b7280;">
                                        No categories yet. Add your first category above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>