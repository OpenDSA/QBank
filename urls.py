from django.conf.urls import patterns, url, include
from django.views.generic import TemplateView

urlpatterns = patterns('',
	url(r'^$', TemplateView.as_view(template_name = "qtool/splashpage.html")),
	url(r'^add/$',  'qtool.views.index'),
	url(r'^simple/$',  'qtool.views.simple'),	
	url(r'^list/$',  'qtool.views.list'),
	url(r'^range/$', 'qtool.views.range'),
	url(r'^edit/(?P<problem_id>\d+)/$',  'qtool.views.edit'),	
	url(r'^delete/(?P<problem_id>\d+)/$',  'qtool.views.delete'),
	url(r'^problems/$', 'qtool.views.problems'),


	url(r'^success/', TemplateView.as_view(template_name = "qtool/success.html")),
	
	url(r'^index/', TemplateView.as_view(template_name = "qtool/index.html")),
	url(r'^help/', TemplateView.as_view(template_name = "qtool/question_setup.html")),
	)


