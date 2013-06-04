from django.conf.urls import patterns, url, include
from django.views.generic import TemplateView

urlpatterns = patterns('',
	url(r'^$', TemplateView.as_view(template_name = "/splashpage.html")),
	url(r'^/$', TemplateView.as_view(template_name = "/splashpage.html")),
	url(r'^add/$',  'qbank.views.index'),
	url(r'^simple/$',  'qbank.views.simple'),	
	url(r'^list/$',  'qbank.views.list'),
	url(r'^range/$', 'qbank.views.range'),
	url(r'^edit/(?P<problem_id>\d+)/$',  'qbank.views.edit'),	
	url(r'^delete/(?P<problem_id>\d+)/$',  'qbank.views.delete'),
	url(r'^problems/$', 'qbank.views.problems'),


	url(r'^success/', TemplateView.as_view(template_name = "qbank/success.html")),
	
	url(r'^index/', TemplateView.as_view(template_name = "qbank/index.html")),
	url(r'^help/', TemplateView.as_view(template_name = "qbank/question_setup.html")),
	)



