<?php
/**
 * APPOLIOS Web Routes
 * Define all application routes here
 */

$router = new Router();

// ============================================
// HOME ROUTES
// ============================================
$router->add('', 'HomeController', 'index', 'GET');
$router->add('/', 'HomeController', 'index', 'GET');
$router->add('home', 'HomeController', 'index', 'GET');
$router->add('about', 'HomeController', 'about', 'GET');
$router->add('contact', 'HomeController', 'contact', 'GET');
$router->add('privacy', 'HomeController', 'privacy', 'GET');
$router->add('terms', 'HomeController', 'terms', 'GET');
$router->add('courses', 'HomeController', 'courses', 'GET');

// ============================================
// AUTH ROUTES
// ============================================
$router->add('login', 'AuthController', 'login', 'GET');
$router->add('authenticate', 'AuthController', 'authenticate', 'POST');
$router->add('register', 'AuthController', 'register', 'GET');
$router->add('signup', 'AuthController', 'signup', 'POST');
$router->add('logout', 'AuthController', 'logout', 'GET');
$router->add('admin/login', 'AuthController', 'adminLogin', 'GET');

// ============================================
// ADMIN ROUTES
// ============================================
$router->add('admin/dashboard', 'AdminController', 'dashboard', 'GET');
$router->add('admin/users', 'AdminController', 'users', 'GET');
$router->add('admin/courses', 'AdminController', 'courses', 'GET');
$router->add('admin/add-course', 'AdminController', 'addCourse', 'GET');
$router->add('admin/store-course', 'AdminController', 'storeCourse', 'POST');
$router->add('admin/edit-course/{id}', 'AdminController', 'editCourse', 'GET');
$router->add('admin/update-course/{id}', 'AdminController', 'updateCourse', 'POST');
$router->add('admin/delete-course/{id}', 'AdminController', 'deleteCourse', 'GET');
$router->add('admin/delete-user/{id}', 'AdminController', 'deleteUser', 'GET');
$router->add('admin/teachers', 'AdminController', 'teachers', 'GET');
$router->add('admin/add-teacher', 'AdminController', 'addTeacher', 'GET');
$router->add('admin/store-teacher', 'AdminController', 'storeTeacher', 'POST');
$router->add('admin/evenements', 'AdminController', 'evenements', 'GET');
$router->add('admin/add-evenement', 'AdminController', 'addEvenement', 'GET');
$router->add('admin/store-evenement', 'AdminController', 'storeEvenement', 'POST');
$router->add('admin/edit-evenement/{id}', 'AdminController', 'editEvenement', 'GET');
$router->add('admin/update-evenement/{id}', 'AdminController', 'updateEvenement', 'POST');
$router->add('admin/delete-evenement/{id}', 'AdminController', 'deleteEvenement', 'GET');
$router->add('admin/evenement-ressources', 'AdminController', 'evenementRessources', 'GET');
$router->add('admin/store-evenement-ressource', 'AdminController', 'storeEvenementRessource', 'POST');
$router->add('admin/update-evenement-ressource/{id}', 'AdminController', 'updateEvenementRessource', 'POST');
$router->add('admin/delete-evenement-ressource/{id}', 'AdminController', 'deleteEvenementRessource', 'POST');
$router->add('admin/evenement-requests', 'AdminController', 'evenementRequests', 'GET');
$router->add('admin/approve-evenement/{id}', 'AdminController', 'approveEvenement', 'POST');
$router->add('admin/reject-evenement/{id}', 'AdminController', 'rejectEvenement', 'POST');
$router->add('admin/chapitres/store-global', 'AdminController', 'chapitresStoreGlobal', 'POST');
$router->add('admin/chapitres/add', 'AdminController', 'chapitresAddGlobal', 'GET');
$router->add('admin/chapitres', 'AdminController', 'chapitres', 'GET');
$router->add('admin/course/{id}/chapitres/store', 'AdminController', 'storeChapter', 'POST');
$router->add('admin/course/{id}/chapitres/add', 'AdminController', 'addChapter', 'GET');
$router->add('admin/course/{id}/chapitres', 'AdminController', 'courseChapitres', 'GET');
$router->add('admin/chapitre/{id}/update', 'AdminController', 'updateChapter', 'POST');
$router->add('admin/chapitre/{id}/delete', 'AdminController', 'deleteChapter', 'GET');
$router->add('admin/chapitre/{id}/edit', 'AdminController', 'editChapter', 'GET');
$router->add('admin/quiz/store', 'AdminController', 'storeQuiz', 'POST');
$router->add('admin/quiz/update/{id}', 'AdminController', 'updateQuiz', 'POST');
$router->add('admin/quiz/delete/{id}', 'AdminController', 'deleteQuiz', 'GET');
$router->add('admin/quiz/edit/{id}', 'AdminController', 'editQuiz', 'GET');
$router->add('admin/quiz/add', 'AdminController', 'addQuiz', 'GET');
$router->add('admin/quiz', 'AdminController', 'quizzes', 'GET');
$router->add('admin/questions/store', 'AdminController', 'storeQuestion', 'POST');
$router->add('admin/questions/update/{id}', 'AdminController', 'updateQuestion', 'POST');
$router->add('admin/questions/delete/{id}', 'AdminController', 'deleteQuestion', 'GET');
$router->add('admin/questions/edit/{id}', 'AdminController', 'editQuestion', 'GET');
$router->add('admin/questions/add', 'AdminController', 'addQuestion', 'GET');
$router->add('admin/questions', 'AdminController', 'questionsBank', 'GET');

// Admin — Social Learning (groupes & discussions)
$router->add('admin/sl-groupes/create', 'GroupeController', 'adminCreate', 'GET');
$router->add('admin/sl-groupes/store', 'GroupeController', 'adminStore', 'POST');
$router->add('admin/sl-groupes/{id}/approve', 'GroupeController', 'adminApprove', 'POST');
$router->add('admin/sl-groupes/{id}/reject', 'GroupeController', 'adminReject', 'POST');
$router->add('admin/sl-groupes/{id}/edit', 'GroupeController', 'adminEdit', 'GET');
$router->add('admin/sl-groupes/{id}/update', 'GroupeController', 'adminUpdate', 'POST');
$router->add('admin/sl-groupes/{id}/delete', 'GroupeController', 'adminDelete', 'GET');
$router->add('admin/sl-groupes', 'GroupeController', 'adminIndex', 'GET');

$router->add('admin/sl-discussions/{id}/approve', 'DiscussionController', 'adminApprove', 'POST');
$router->add('admin/sl-discussions/{id}/reject', 'DiscussionController', 'adminReject', 'POST');
$router->add('admin/sl-discussions/{id}/edit', 'DiscussionController', 'adminEdit', 'GET');
$router->add('admin/sl-discussions/{id}/update', 'DiscussionController', 'adminUpdate', 'POST');
$router->add('admin/sl-discussions/{id}/delete', 'DiscussionController', 'adminDelete', 'GET');
$router->add('admin/sl-discussions', 'DiscussionController', 'adminIndex', 'GET');

// ============================================
// STUDENT ROUTES
// ============================================
$router->add('student/dashboard', 'StudentController', 'dashboard', 'GET');
$router->add('student/espace', 'StudentController', 'espace', 'GET');
$router->add('student/chapitres', 'StudentController', 'chapitres', 'GET');
$router->add('student/quiz-history', 'StudentController', 'quizHistory', 'GET');
$router->add('student/quiz/{id}/submit', 'StudentController', 'submitQuiz', 'POST');
$router->add('student/quiz/{id}', 'StudentController', 'takeQuiz', 'GET');
$router->add('student/quiz', 'StudentController', 'quizList', 'GET');
$router->add('student/questions', 'StudentController', 'questionsBank', 'GET');
$router->add('student/evenements', 'StudentController', 'evenements', 'GET');
$router->add('student/evenement/{id}', 'StudentController', 'evenementDetail', 'GET');
$router->add('student/courses', 'StudentController', 'courses', 'GET');
$router->add('student/course/{id}', 'StudentController', 'viewCourse', 'GET');
$router->add('student/enroll/{id}', 'StudentController', 'enroll', 'GET');
$router->add('student/my-courses', 'StudentController', 'myCourses', 'GET');
$router->add('student/profile', 'StudentController', 'profile', 'GET');

// Student / Teacher — Social Learning (groupes & discussions) — demandes soumises à validation admin
// Ordre : routes les plus spécifiques d’abord (éviter qu’un segment mange le suivant).
$router->add('student/groupes/{idGroupe}/discussions/{idDisc}/messages/store', 'MessageController', 'store', 'POST');
$router->add('student/groupes/{idGroupe}/discussions/{idDisc}/messages/{id}/delete', 'MessageController', 'destroy', 'GET');
$router->add('student/groupes/{idGroupe}/discussions/{id}/update', 'DiscussionController', 'update', 'POST');
$router->add('student/groupes/{idGroupe}/discussions/{id}/like', 'DiscussionController', 'like', 'POST');
$router->add('student/groupes/{idGroupe}/discussions/{id}/delete', 'DiscussionController', 'destroy', 'GET');
$router->add('student/groupes/{idGroupe}/discussions/create', 'DiscussionController', 'create', 'GET');
$router->add('student/groupes/{idGroupe}/discussions/store', 'DiscussionController', 'store', 'POST');
$router->add('student/groupes/{idGroupe}/discussions/{id}', 'DiscussionController', 'show', 'GET');
$router->add('student/groupes/{idGroupe}/discussions', 'DiscussionController', 'index', 'GET');
$router->add('student/groupes/create', 'GroupeController', 'create', 'GET');
$router->add('student/groupes/store', 'GroupeController', 'store', 'POST');
$router->add('student/groupes/{id}/edit', 'GroupeController', 'edit', 'GET');
$router->add('student/groupes/{id}/update', 'GroupeController', 'update', 'POST');
$router->add('student/groupes/{id}/join', 'GroupeController', 'join', 'GET');
$router->add('student/groupes/{id}/leave', 'GroupeController', 'leave', 'GET');
$router->add('student/groupes/{id}/delete', 'GroupeController', 'destroy', 'GET');
$router->add('student/groupes/{id}', 'GroupeController', 'show', 'GET');
$router->add('student/groupes', 'GroupeController', 'index', 'GET');

$router->add('teacher/groupes/{idGroupe}/discussions/{idDisc}/messages/store', 'MessageController', 'store', 'POST');
$router->add('teacher/groupes/{idGroupe}/discussions/{idDisc}/messages/{id}/delete', 'MessageController', 'destroy', 'GET');
$router->add('teacher/groupes/{idGroupe}/discussions/{id}/update', 'DiscussionController', 'update', 'POST');
$router->add('teacher/groupes/{idGroupe}/discussions/{id}/like', 'DiscussionController', 'like', 'POST');
$router->add('teacher/groupes/{idGroupe}/discussions/{id}/delete', 'DiscussionController', 'destroy', 'GET');
$router->add('teacher/groupes/{idGroupe}/discussions/create', 'DiscussionController', 'create', 'GET');
$router->add('teacher/groupes/{idGroupe}/discussions/store', 'DiscussionController', 'store', 'POST');
$router->add('teacher/groupes/{idGroupe}/discussions/{id}', 'DiscussionController', 'show', 'GET');
$router->add('teacher/groupes/{idGroupe}/discussions', 'DiscussionController', 'index', 'GET');
$router->add('teacher/groupes/create', 'GroupeController', 'create', 'GET');
$router->add('teacher/groupes/store', 'GroupeController', 'store', 'POST');
$router->add('teacher/groupes/{id}/edit', 'GroupeController', 'edit', 'GET');
$router->add('teacher/groupes/{id}/update', 'GroupeController', 'update', 'POST');
$router->add('teacher/groupes/{id}/join', 'GroupeController', 'join', 'GET');
$router->add('teacher/groupes/{id}/leave', 'GroupeController', 'leave', 'GET');
$router->add('teacher/groupes/{id}/delete', 'GroupeController', 'destroy', 'GET');
$router->add('teacher/groupes/{id}', 'GroupeController', 'show', 'GET');
$router->add('teacher/groupes', 'GroupeController', 'index', 'GET');

// ============================================
// TEACHER ROUTES
// ============================================
$router->add('teacher/dashboard', 'TeacherController', 'dashboard', 'GET');
$router->add('teacher/courses', 'TeacherController', 'myCourses', 'GET');
$router->add('teacher/add-course', 'TeacherController', 'addCourse', 'GET');
$router->add('teacher/store-course', 'TeacherController', 'storeCourse', 'POST');
$router->add('teacher/edit-course/{id}', 'TeacherController', 'editCourse', 'GET');
$router->add('teacher/update-course/{id}', 'TeacherController', 'updateCourse', 'POST');
$router->add('teacher/delete-course/{id}', 'TeacherController', 'deleteCourse', 'GET');
$router->add('teacher/course/{id}', 'TeacherController', 'viewCourse', 'GET');
$router->add('teacher/profile', 'TeacherController', 'profile', 'GET');
$router->add('teacher/evenements', 'TeacherController', 'evenements', 'GET');
$router->add('teacher/add-evenement', 'TeacherController', 'addEvenement', 'GET');
$router->add('teacher/store-evenement', 'TeacherController', 'storeEvenement', 'POST');
$router->add('teacher/edit-evenement/{id}', 'TeacherController', 'editEvenement', 'GET');
$router->add('teacher/update-evenement/{id}', 'TeacherController', 'updateEvenement', 'POST');
$router->add('teacher/delete-evenement/{id}', 'TeacherController', 'deleteEvenement', 'GET');
$router->add('teacher/evenement-ressources', 'TeacherController', 'evenementRessources', 'GET');
$router->add('teacher/store-evenement-ressource', 'TeacherController', 'storeEvenementRessource', 'POST');
$router->add('teacher/update-evenement-ressource/{id}', 'TeacherController', 'updateEvenementRessource', 'POST');
$router->add('teacher/delete-evenement-ressource/{id}', 'TeacherController', 'deleteEvenementRessource', 'POST');
$router->add('teacher/chapitres/store-global', 'TeacherController', 'chapitresStoreGlobal', 'POST');
$router->add('teacher/chapitres/add', 'TeacherController', 'chapitresAddGlobal', 'GET');
$router->add('teacher/chapitres', 'TeacherController', 'chapitres', 'GET');
$router->add('teacher/course/{id}/chapitres/store', 'TeacherController', 'storeChapter', 'POST');
$router->add('teacher/course/{id}/chapitres/add', 'TeacherController', 'addChapter', 'GET');
$router->add('teacher/course/{id}/chapitres', 'TeacherController', 'courseChapitres', 'GET');
$router->add('teacher/chapitre/{id}/update', 'TeacherController', 'updateChapter', 'POST');
$router->add('teacher/chapitre/{id}/delete', 'TeacherController', 'deleteChapter', 'GET');
$router->add('teacher/chapitre/{id}/edit', 'TeacherController', 'editChapter', 'GET');
$router->add('teacher/quiz/store', 'TeacherController', 'storeQuiz', 'POST');
$router->add('teacher/quiz/update/{id}', 'TeacherController', 'updateQuiz', 'POST');
$router->add('teacher/quiz/delete/{id}', 'TeacherController', 'deleteQuiz', 'GET');
$router->add('teacher/quiz/edit/{id}', 'TeacherController', 'editQuiz', 'GET');
$router->add('teacher/quiz/add', 'TeacherController', 'addQuiz', 'GET');
$router->add('teacher/quiz', 'TeacherController', 'quizzes', 'GET');
$router->add('teacher/questions/store', 'TeacherController', 'storeQuestion', 'POST');
$router->add('teacher/questions/update/{id}', 'TeacherController', 'updateQuestion', 'POST');
$router->add('teacher/questions/delete/{id}', 'TeacherController', 'deleteQuestion', 'GET');
$router->add('teacher/questions/edit/{id}', 'TeacherController', 'editQuestion', 'GET');
$router->add('teacher/questions/add', 'TeacherController', 'addQuestion', 'GET');
$router->add('teacher/questions', 'TeacherController', 'questionsBank', 'GET');

// ============================================
// SOCIAL LEARNING MODULE (proxied through main app)
// ============================================
$router->add('social-learning/groupe', 'SocialLearningController', 'groupeIndex', 'GET');
$router->add('social-learning/groupe/create', 'SocialLearningController', 'groupeCreate', 'GET');
$router->add('social-learning/groupe/store', 'SocialLearningController', 'groupeStore', 'POST');
$router->add('social-learning/groupe/show/{id}', 'SocialLearningController', 'groupeShow', 'GET');
$router->add('social-learning/groupe/edit/{id}', 'SocialLearningController', 'groupeEdit', 'GET');
$router->add('social-learning/groupe/update/{id}', 'SocialLearningController', 'groupeUpdate', 'POST');
$router->add('social-learning/groupe/delete/{id}', 'SocialLearningController', 'groupeDelete', 'GET');

$router->add('social-learning/discussion', 'SocialLearningController', 'discussionIndex', 'GET');
$router->add('social-learning/discussion/create', 'SocialLearningController', 'discussionCreate', 'GET');
$router->add('social-learning/discussion/store', 'SocialLearningController', 'discussionStore', 'POST');
$router->add('social-learning/discussion/show/{id}', 'SocialLearningController', 'discussionShow', 'GET');
$router->add('social-learning/discussion/edit/{id}', 'SocialLearningController', 'discussionEdit', 'GET');
$router->add('social-learning/discussion/update/{id}', 'SocialLearningController', 'discussionUpdate', 'POST');
$router->add('social-learning/discussion/delete/{id}', 'SocialLearningController', 'discussionDelete', 'GET');

$router->add('social-learning/message/store', 'SocialLearningController', 'messageStore', 'POST');
$router->add('social-learning/message/delete/{id}', 'SocialLearningController', 'messageDelete', 'GET');

return $router;