from django.conf.urls import patterns, url, include
from django.views.generic import TemplateView

urlpatterns = patterns('',
	url(r'^$', TemplateView.as_view(template_name = "splashpage.html")),
	url(r'^/$', TemplateView.as_view(template_name = "splashpage.html")),
	url(r'^about/$', TemplateView.as_view(template_name = "about.html")),
	url(r'^index/', TemplateView.as_view(template_name = "index.html")),
	url(r'^help/', TemplateView.as_view(template_name = "help.html")),
	url(r'^contact/', TemplateView.as_view(template_name = "contact.html")),

	url(r'^add/$',  'qbank.views.index'),
	url(r'^simple/$',  'qbank.views.simple'),	
	url(r'^list/$',  'qbank.views.list'),
	url(r'^range/$', 'qbank.views.range'),
	url(r'^summative/$', 'qbank.views.summative'),

	url(r'^edit/(?P<problem_id>\d+)/$',  'qbank.views.edit'),
	
	url(r'^delete/(?P<problem_id>\d+)/$',  'qbank.views.delete'),

	url(r'^problems/$', 'qbank.views.problems'),
	url(r'^export/$', 'qbank.views.export'),
	url(r'^problems_Summary/$', 'qbank.views.problems_Summary'),
	
	url(r'^(?P<problem_id>\d+)/ka_details', 'qbank.views.ka_details'),
	url(r'^edit_ka/(?P<problem_id>\d+)/$',  'qbank.views.edit_ka'),
	url(r'^(?P<problem_id>\d+)/ka_gen', 'qbank.views.ka_gen'),
	
	url(r'^(?P<problem_id>\d+)/simple_details', 'qbank.views.simple_details'),
	url(r'^edit_simple/(?P<problem_id>\d+)/$',  'qbank.views.edit_simple'),
	
	url(r'^(?P<problem_id>\d+)/list_details', 'qbank.views.list_details'),
	url(r'^edit_list/(?P<problem_id>\d+)/$',  'qbank.views.edit_list'),
	
	url(r'^(?P<problem_id>\d+)/range_details', 'qbank.views.range_details'),
	url(r'^edit_range/(?P<problem_id>\d+)/$',  'qbank.views.edit_range'),
	
	url(r'^(?P<problem_id>\d+)/summative_details', 'qbank.views.summative_details'),
	url(r'^edit_summative/(?P<problem_id>\d+)/$',  'qbank.views.edit_summative'),
	
	url(r'^(?P<problem_id>\d+)/write_file', 'qbank.views.write_file'),
	url(r'^(?P<problem_id>\d+)/ka_error', 'qbank.views.ka_error'),
	url(r'^(?P<problem_id>\d+)/d$',  'qbank.views.d'),
	url(r'^(?P<path>.*)$', 'django.views.static.serve',
	{'document_root': '/home/OpenDSA/'}),
	)


